pipeline{
    agent any
    stages{
        stage('Build'){
            steps{
                script{
                    dockerapp = docker.build("thormesfap/mentorr-backend-live:${env.BUILD_NUMBER}")
                }
            }
        }
        stage('Registry'){
            steps{
                script{
                    docker.withRegistry('https://index.docker.io/v1/', 'dockerhub'){
                        dockerapp.push()
                    }
                }
            }
        }
    }
}
