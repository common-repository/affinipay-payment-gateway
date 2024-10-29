#!/usr/bin/env groovy
internalRepo = "git@bitbucket.org:affinipay/affinipay-wordpress-plugin.git"
publicRepo = "git@github.com:affinipay/affinipay-wordpress-plugin.git"
targetBranch = "master"

// Build parameterts
properties([[$class: 'ParametersDefinitionProperty',
             parameterDefinitions:
                 [[$class: 'StringParameterDefinition', name: 'UserEmail', defaultValue: 'usharma@affinipay.com'],
                 [$class: 'StringParameterDefinition', name: 'TestName', defaultValue: '*']]
          ]])

/*
 * Entry point: Select build type
 */
try {
  if(isReleaseBuild()){
    performRelease()
  }
  else {
    performBuild()
  }
  currentBuild.result = 'SUCCESS'
  notifyBuildResult(currentBuild.result)
  bitbucketStatusNotify(buildState: 'SUCCESSFUL', credentialsId: '6e598f39-dc2c-45c3-8e2e-20eb59439759')
}
// Catch an handle errors in build pipeline
catch(e) {
  teardown()
  currentBuild.result = 'FAILED'
  notifyBuildResult(currentBuild.result)
  emailFailed("uharma@affinipay.com", e)
  bitbucketStatusNotify(buildState: 'FAILED', credentialsId: '6e598f39-dc2c-45c3-8e2e-20eb59439759')
  throw(e)
}

/*
 * Build flows
 */
def performBuild(){
  node("mono"){
    lock('wordpress_plugin_docker') {
      stage_Prepare()
      stage_Build()
      stage_Test()
      stage_Finish()
      if (env.BRANCH_NAME == targetBranch) {
        stage_Tag()
      }
    }
  }
}

def performRelease(){
  node("mono"){
    commitMessageInput = stage_Confirm()
    stage_Prepare(true)
    stage_Publish(commitMessageInput)
  }
}

/*
 * Build stages
 */
def stage_Prepare(isReleaseBuild = false){
  stage("Checkout"){
    // Define environment variables
    env.TEST_RESULTS = 'reports/junit.xml'

    // Clear directory
    sh "rm -rf *"

    // Check out source
    // https://stackoverflow.com/a/38255364/123336
    def scmUrl = scm.getUserRemoteConfigs()[0].getUrl()
    def scmCredentialsId = scm.getUserRemoteConfigs()[0].getCredentialsId()
    checkout([$class: 'GitSCM',
        branches: [[name: "${env.BRANCH_NAME}"]],
        doGenerateSubmoduleConfigurations: false,
        extensions: [[$class: 'SubmoduleOption',
            disableSubmodules: false,
            parentCredentials: false,
            recursiveSubmodules: true,
            reference: '',
            trackingSubmodules: false]],
        submoduleCfg: [],
        userRemoteConfigs: [[credentialsId: scmCredentialsId, url: scmUrl]]])

    // Clean up tags
    sh "git fetch"
    sh "git tag -l | xargs git tag -d && git fetch -t"
    if (!isReleaseBuild) {
      sh "git reset --hard origin/${env.BRANCH_NAME}"
    }

    // Check out common functions
    library identifier: 'jenkins-pipeline-library@client-libraries', retriever: modernSCM(
      [$class: 'GitSCMSource',
      remote: 'git@bitbucket.org:affinipay/jenkins-pipeline-library.git',
      credentialsId: scm.getUserRemoteConfigs()[0].getCredentialsId()])

    // Update build status
    bitbucketStatusNotify(buildState: 'INPROGRESS', credentialsId: '6e598f39-dc2c-45c3-8e2e-20eb59439759')
  }
}

def stage_Build() {
  stage('Build') {
      nvm("10.15.3") {
          teardown()
          bringup()
      }
      for (i = 0; i <5; i++) {
          echo "."
          sleepFunc()
      }
  }
}

def stage_Test(){
  stage("Test"){
    sh '''
    #!/bin/bash -l
    cd lib
    ./run_tests.sh
    '''
    junit 'lib/junit.xml'
  }
}

def stage_Tag(){
  stage("Tag"){
    def versionName = getVersion()
    gitTag versionName
  }
}

def stage_Confirm(){
  def inputMessage = ""
  stage("Confirm"){
      inputMessage = input(
          id: 'CommitMessage', message: 'input your commit message: ', ok: 'Ok', parameters: [string(defaultValue: '', description: 'You can press Ok to use the default commit message "Changes for <release version>"', name: 'COMMIT_MSG')]
      )
  }
  return inputMessage
}

def stage_Publish(commitMessage){
  stage("Publish"){
      // TODO: Publish via jenkins
  }
}

def stage_Finish() {
    teardown()
}
/*
 * Helpers
 */

def bringup() {
    sh '''
    #!/bin/bash -l
    ./bringup
    '''
}

def teardown() {
    sh '''
    #!/bin/bash -l
    echo "y" | ./teardown
    '''
}

@NonCPS
def sleepFunc() {
    sleep(1)
}
boolean isReleaseBuild() { return env.BRANCH_NAME ==~ /^v[(\d+\.\d)]+\-b\d+$/ }

def getVersion(includeBuildNumber = true){
    // Get version
    def outputVersion = sh(
            returnStdout: true,
            script: "cat lib/chargeio/version.rb | grep -o '\".*\"' | sed 's@.*\"\\(.*\\)\".*@\\1@' "
    ).replace('version:','').replace('\n','')
    def versionString = "v${outputVersion}"
    if (includeBuildNumber) versionString = versionString + "-b${env.BUILD_NUMBER}"
    return versionString
}

def notifyBuildResult(String result) {
    def colorCode = (result == 'SUCCESS') ? '#36A64F' : '#D00000'
    def msg = "${env.JOB_NAME} [${env.BUILD_NUMBER}] ${result} (<${env.BUILD_URL}|Open>)"
    // slackSend (channel: "#devtools", color: colorCode, message: msg)
}

def emailFailed(email_to, error) {
    def msg = null
    if (!error) {
        msg = 'Error during build. You should fix it.\n${env.BUILD_URL}'
    }
    else {
        msg = "Error during ${env.STAGE_NAME} stage. You should fix it.\n${env.BUILD_URL} \n\n Error:\n ${error.getMessage()}"
    }
    mail subject: "Something is wrong with ${env.JOB_NAME} ${env.BUILD_ID}",
    to: email_to,
    body: msg
}
