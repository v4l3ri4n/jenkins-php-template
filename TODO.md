TODO
====

- implement coverage report
- debug style output error on phpspec reports
- implement taurus http://gettaurus.org
- expose tests outputs for : phpdepend, violations (not compatible with pipeline), maybe external sh script or xsl transformation, or call to other job
- phpmetrics not generating xml outputs
- run tests over docker images
- template for the different stage :
    - publish on DEV environment
    - publish on STAGING environment with prompt (staging: as near as possible as the prod env)
    - publish on PRODUCTION environment with prompt