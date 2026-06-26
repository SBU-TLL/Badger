//#tool nuget:?package=NUnit.ConsoleRunner&version=3.4.0
#tool nuget:?package=GitVersion.CommandLine&version=4.0.0
#addin nuget:?package=Cake.Incubator&version=6.0.0
#tool "nuget:?package=OctopusTools&version=7.4.3424"
#addin nuget:?package=SharpZipLib&version=1.3.1
#addin nuget:?package=Cake.Compression&version=0.2.6
#addin "Cake.FileHelpers&version=4.0.1"


//////////////////////////////////////////////////////////////////////
// ARGUMENTS
//////////////////////////////////////////////////////////////////////

var target = Argument("target", "Default");
var configuration = Argument("configuration", "Release");

//////////////////////////////////////////////////////////////////////
// PREPARATION
//////////////////////////////////////////////////////////////////////

// Define directories.
//This is the directory where MSBuild will saves build files
var buildDir = Directory("./www");
//This is directory where we will save zip file of build artifact
var artifactDir = Directory("./artifacts");

// Define project metadata properties.
var projectOwners = "Stony Brook University";
var projectName = "CBPE.ORSEE";

var projectDescription = "ORSEE project for CBPE";

// Get/set version information.
var versionInfo = GitVersion();


var buildNumber = Bamboo.Environment.Build.Number.ToString();

Information("Build Number: {0}", buildNumber);

var buildNumberPadded = Bamboo.Environment.Build.Number.ToString("00000");
var isBuildSystemBuild = BuildSystem.IsRunningOnBamboo;

var deploymentServer =  Argument("OCTO_SERVER", "");
var deploymentApiKey = Argument("OCTO_KEY", "");

//only the following branches will be released to octopus
string[] releasableBranches = { "develop", "release", "hotfix", "support", "master", "default"};
var shouldCreateRelease = (releasableBranches.Any(versionInfo.BranchName.ToString().ToLower().StartsWith));

//If we are running on the build server then the octopus deploy details are available as environment variables instead of being passed in.
if(isBuildSystemBuild) {
    Information("---Running on Bamboo Build Server---");

    if(deploymentServer == "") {
        deploymentServer = EnvironmentVariable("bamboo_OCTO_SERVER") ?? "";
    }

    if(deploymentApiKey == "" ) {
        deploymentApiKey = EnvironmentVariable("bamboo_OCTO_KEY") ?? "";
    }
}

var assemblyVersion = string.Concat(new string[]{
    versionInfo.Major.ToString(),
    ".",
    versionInfo.Minor.ToString(),
    ".0.0"
});
var assemblyFileVersion = string.Concat(new string[]{
    versionInfo.MajorMinorPatch,
    ".",
    buildNumber
});
var assemblyInformationalVersion = versionInfo.InformationalVersion;
var semanticVersion = versionInfo.FullSemVer;


var zipArchivePath =  MakeAbsolute(artifactDir).FullPath + "/" + projectName + "." + semanticVersion + ".zip";


//////////////////////////////////////////////////////////////////////
// TASKS
//////////////////////////////////////////////////////////////////////

Task("Clean")
    .Does(() =>
    {
        CleanDirectory(artifactDir);
    });


Task("Version")
    .IsDependentOn("Clean")
    .Does(() =>
    {
           Information("Is Build System Build: {0}", isBuildSystemBuild);

        Information("Assembly Version: {0}", assemblyVersion);
        Information("Assembly File Version: {0}", assemblyFileVersion);
        Information("Assembly Informational Version: {0}", assemblyInformationalVersion);

        Information("Build Number: {0}", buildNumber);

        //Information("NuGet Version: {0}", nugetPackageVersion);
        Information("VCS Revision: {0}", versionInfo.Sha);
        Information("VCS Branch Name: {0}", versionInfo.BranchName);
        Information("Is Releasable Branch? {0}", shouldCreateRelease);

        Information("GitVersion Info:\r\n{0}", versionInfo.Dump());
       
    });



Task("Pack")
    .IsDependentOn("Version")
    .Does(() =>
    {
        Information("-----Packing Files for Deployment-----");

        

        Information("Making artifact from {0} and zipping into {1}", buildDir, zipArchivePath);

            ZipCompress(
                MakeAbsolute(buildDir),
                zipArchivePath
            );
        
    });
Task("OctoPush")
    .IsDependentOn("Pack")
    .Does(() =>
    {

        if(!shouldCreateRelease) {
            Information("***Branch {0} Isn't Releasable Not Pushing To Octopus***", versionInfo.BranchName);
        }
        else if(deploymentApiKey == "" || deploymentServer == "") {
            Information("***Octopus Deployment Server or Key Not Specified***");
        }
        else { //if(shouldCreateRelease && deploymentApiKey != "" && deploymentServer != "") {

            Information("-----Pushing to Octopus----");

            OctoPush(deploymentServer, deploymentApiKey, new FilePath(zipArchivePath),
                new OctopusPushSettings {
                    ReplaceExisting = true
                }
            );
        }
    });

Task("OctoRelease")
    .IsDependentOn("OctoPush")
    .Does(() =>
    {
        if(!shouldCreateRelease) {
            Information("***Branch {0} Isn't Releasable Not Releasing To Octopus***", versionInfo.BranchName);
        }
        else if(deploymentApiKey == "" || deploymentServer == "") {
            Information("***Octopus Deployment Server or Key Not Specified***");
        }
        else {
            Information("-----Releasing to Octopus----");

            OctoCreateRelease(projectName, new CreateReleaseSettings {
                Server = deploymentServer,
                ApiKey = deploymentApiKey,
                DefaultPackageVersion = semanticVersion,
                ReleaseNumber = semanticVersion
            });
        }
    });


//////////////////////////////////////////////////////////////////////
// TASK TARGETS
//////////////////////////////////////////////////////////////////////

Task("Default")
    .IsDependentOn("OctoRelease");

//////////////////////////////////////////////////////////////////////
// EXECUTION
//////////////////////////////////////////////////////////////////////

RunTarget(target);