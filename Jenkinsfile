pipeline {
    agent any
    
    environment {
        SOURCE_DIR = 'src'
        TESTS_DIR = 'tests'
        BIN_PHP = 'php'
        BIN_PHPCS = 'phpcs'
        BIN_PHPDEPEND = 'pdepend'
        BIN_PHPMD = 'phpmd'
        BIN_PHPCPD = 'phpcpd'
        BIN_PHPLOC = 'phploc'
        BIN_PHPDOX = 'phpdox'
        BIN_PHPDOC = 'phpDocumentor'
        BIN_PHPMETRICS = 'phpmetrics'
        BIN_PHPCB = '/home/valerian/.composer/vendor/bin/phpcb'
        BIN_PHPUNIT = 'phpunit'
        BIN_PHPSPEC = 'vendor/bin/phpspec'
        BIN_BEHAT = 'behat'
    }
    
    stages {
        stage('Build') {
            steps {
                sh 'echo "Build"'
                checkout scm
                sh 'rm -rf build/*'
                sh 'mkdir -p build/api/phpdox'
                sh 'mkdir -p build/api/phpdoc'
                sh 'mkdir -p build/code-browser'
                sh 'mkdir -p build/coverage'
                sh 'mkdir -p build/tests'
                sh 'mkdir -p build/tests/behat'
                sh 'mkdir -p build/tests/phpspec'
                sh 'mkdir -p build/tests/phpunit'
                sh 'mkdir -p build/logs'
                sh 'mkdir -p build/phpmetrics'
                sh 'mkdir -p build/pdepend'
                sh 'mkdir -p build/phpdox'
                sh 'composer install'
            }
        }
        stage('Code analysis') {
            steps {
                parallel (
                    lint: {
                        sh '$BIN_PHP -l $SOURCE_DIR'
                    },
                    linttests: {
                        sh '$BIN_PHP -l $TESTS_DIR'
                    },
                    phpcs: {
                        sh '$BIN_PHPCS --report=checkstyle --report-file=build/logs/checkstyle.xml --standard=phpcs.xml --extensions=php --runtime-set ignore_errors_on_exit 1 --runtime-set ignore_warnings_on_exit 1 $SOURCE_DIR'
                    },
                    pdepend: {
                        sh '$BIN_PHPDEPEND --jdepend-xml=build/logs/jdepend.xml --jdepend-chart=build/pdepend/dependencies.svg --overview-pyramid=build/pdepend/overview-pyramid.svg $SOURCE_DIR'
                    },
                    phpmd: {
                        sh '$BIN_PHPMD $SOURCE_DIR xml phpmd.xml --reportfile build/logs/pmd.xml --ignore-violations-on-exit'
                    },
                    phpcpd: {
                        sh '$BIN_PHPCPD --log-pmd build/logs/pmd-cpd.xml $SOURCE_DIR'
                    },
                    phpdox: {
                        sh '$BIN_PHPDOX -f phpdox.xml'
                    },
                    phpdoc: {
                        sh '$BIN_PHPDOC -d $SOURCE_DIR -t build/api/phpdoc --title="API Documentation"'
                    },
                    phpcb: {
                        sh '$BIN_PHPCB --log build/logs --output build/code-browser --source $SOURCE_DIR'
                    },
                    phploc: {
                        sh '$BIN_PHPLOC --count-tests --log-csv build/logs/phploc.csv --log-xml build/logs/phploc.xml $SOURCE_DIR $TESTS_DIR'
                    },
                    phpmetrics: {
                        sh '$BIN_PHPMETRICS --report-xml=build/phpmetrics/phpmetrics.xml --violations-xml=build/phpmetrics/violations.xml --report-html=build/phpmetrics/quality.html $SOURCE_DIR'
                    }
                )
            }
            post {
                success {
					//
					// publish phpcs output
					//
                    step(
                        [
                            $class: 'CheckStylePublisher',
                            pattern: 'build/logs/checkstyle.xml',
                            alwaysLinkToLastBuild: true,
                            usePreviousBuildAsReference: false
                        ]
                    )
					//
					// publish phpdepend outputs
					//
                    publishHTML(
                        target: [
                            reportName: 'PDepend Pyramid graph',
                            reportDir: 'build/pdepend',
                            reportFiles: 'overview-pyramid.svg',
                            keepAll: true
                        ]
                    )
                    publishHTML(
                        target: [
                            reportName: 'PDepend Dependencies graph',
                            reportDir: 'build/pdepend',
                            reportFiles: 'dependencies.svg',
                            keepAll: true
                        ]
                    )
					//
					// publish phpmd output
					//
                    step(
                        [
                            $class: 'PmdPublisher',
                            canComputeNew: false,
                            defaultEncoding: '',
                            pattern: 'build/logs/pmd.xml',
                            healthy: '70',
                            unHealthy: '999',
                            unstableTotalAll: '999'
                        ]
                    )
					//
					// publish phpcpd output
					//
                    step(
                        [
                            $class: 'DryPublisher',
                            pattern: 'build/logs/pmd-cpd.xml',
                            highThreshold: 50,
                            normalThreshold: 25
                        ]
                    )
					//
					// publish phpdow output
					//
                    publishHTML(
                        target: [
                            reportName: 'API Documentation (phpdox)',
                            reportDir: 'build/api/phpdox/html/',
                            reportFiles: 'index.xhtml',
                            keepAll: true
                        ]
                    )
					//
					// publish phdoc output
					//
                    publishHTML(
                        target: [
                            reportName: 'API Documentation (phpdoc)',
                            reportDir: 'build/api/phpdoc/',
                            reportFiles: 'index.html',
                            keepAll: true
                        ]
                    )
					//
					// publish phploc output
					//
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-1.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Lines of Code (LOC),Comment Lines of Code (CLOC),Non-Comment Lines of Code (NCLOC),Logical Lines of Code (LLOC)', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false,  group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'A - Lines of code', useDescr: false, yaxis: 'Lines of code', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-2.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Directories,Files,Namespaces,Interfaces,Classes,Methods,Functions,Anonymous Functions,Constants', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'B - Structures Containers', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-3.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Test Classes,Test Methods', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'C - Testing', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-4.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Classes,Abstract Classes,Concrete Classes', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'D - Types of Classes', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-5.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Methods,Non-Static Methods,Static Methods,Public Methods,Non-Public Methods', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'E - Types of Methods', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-6.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Constants,Global Constants,Class Constants', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'F - Types of Constants', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-7.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Functions,Named Functions,Anonymous Functions', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'G - Types of Functions', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-8.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Average Class Length (LLOC),Average Method Length (LLOC),Average Function Length (LLOC)', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'H - Average Length', useDescr: false, yaxis: 'Average Non-Comment Lines of Code', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-9.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Cyclomatic Complexity / Lines of Code,Cyclomatic Complexity / Number of Methods', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'I - Relative Cyclomatic Complexity', useDescr: false, yaxis: 'Cyclomatic Complexity by Structure', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-10.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Logical Lines of Code (LLOC),Classes Length (LLOC),Functions Length (LLOC),LLOC outside functions or classes', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'AB - Code Structure by Logical Lines of Code', useDescr: false, yaxis: 'Logical Lines of Code', yaxisMaximum: '', yaxisMinimum: ''])
                    step([$class: 'PlotBuilder', csvFileName: 'plot-phploc-11.csv', csvSeries: [[displayTableFlag: false, exclusionValues: 'Interfaces,Traits,Classes,Methods,Functions,Constants', file: 'build/logs/phploc.csv', inclusionFlag: 'INCLUDE_BY_STRING', url: '']], exclZero: false, group: 'phploc', keepRecords: false, logarithmic: false, numBuilds: '100', style: 'line', title: 'BB - Structure Objects', useDescr: false, yaxis: 'Count', yaxisMaximum: '', yaxisMinimum: ''])
					//
					// publish phpcb output
					//
                    publishHTML(
                        target: [
                            reportName: 'Code Browser',
                            reportDir: 'build/code-browser',
                            reportFiles: 'index.html',
                            keepAll: true
                        ]
                    )
					//
					// publish phpmetrics output
					//
                    publishHTML(
                        target: [
                            reportName: 'PhpMetrics',
                            reportDir: 'build/phpmetrics/quality.html/',
                            reportFiles: 'index.html',
                            keepAll: true
                        ]
                    )
                }
            }
        }
        stage('Tests') {
            steps {
                parallel (
                    phpunit: {
                        sh '$BIN_PHPUNIT --configuration phpunit.xml'
                    },
                    phpspec: {
                        sh '$BIN_PHPSPEC run --format html > build/tests/phpspec/report.html'
                    },
                    phpspecjunit: {
                        sh '$BIN_PHPSPEC run --format junit > build/tests/phpspec/junit.xml'
                    },
                    behat: {
                        sh '$BIN_BEHAT --format pretty --out build/tests/behat/report.txt --format junit --out build/tests/behat --format cucumber_json'
                    }
                )
            }
            post {
                success {
					//
					// publish phpunit output
					//
                    junit 'build/tests/phpunit/junit.xml'
					//
					// publish phpspec output
					//
                    junit 'build/tests/phpspec/junit.xml'
                    publishHTML(
                        target: [
                            reportName: 'phpspec',
                            reportDir: 'build/tests/phpspec/',
                            reportFiles: 'report.html',
                            keepAll: true
                        ]
                    )
					//
					// publish behat output
					//
                    junit 'build/tests/behat/default.xml'
                    publishHTML(
                        target: [
                            reportName: 'Behat raw output',
                            reportDir: 'build/tests/behat/',
                            reportFiles: 'report.txt',
                            keepAll: true
                        ]
                    )
                    step([$class: 'CucumberReportPublisher', failedFeaturesNumber: 0, failedScenariosNumber: 0, failedStepsNumber: 0, fileExcludePattern: '', fileIncludePattern: '**/*.json', jsonReportDirectory: 'build/tests/behat', pendingStepsNumber: 0, skippedStepsNumber: 0, undefinedStepsNumber: 0])
                }
            }
        }
        stage('Deploy') {
            steps {
                sh 'echo "Deploy stage"'
            }
        }
        stage('Cleanup') {
            steps {
                sh 'echo "Cleanup stage"'
            }
        }
    }
    post {
        always {
            echo 'One way or another, I have finished'
        }
        success {
            echo 'I succeeeded!'
        }
        unstable {
            echo 'I am unstable :/'
        }
        failure {
            echo 'I failed :('
        }
        changed {
            echo 'Things were different before...'
        }
    }
}
