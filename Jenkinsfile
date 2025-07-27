pipeline{
    agent any
    environment{
        WORKLOAD = 'backend-live'
        CONTAINER = 'devops-backend-live'
        IMAGE = "thormesfap/mentorr-backend-live:${env.BUILD_NUMBER}"
    }
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
        stage('Deploy'){
            steps{
                sh """
                    docker exec \$(docker ps -q) kubectl set image deployment/\${WORKLOAD} \${CONTAINER}=\${IMAGE} -n default
                """
            }
        }
    }
}
