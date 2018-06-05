<?php

return array(
    'Data Entry'=>array(
        'access'=>'DE',
        'items'=>array(
            'Credit application'=>array(
                'access'=>'DE01',
                'url'=>'/addIntegral/index',
            ),
            'Apply list'=>array(
                'access'=>'DE02',
                'url'=>'/integral/index',
            ),
        ),
    ),
    'Exchange'=>array(
        'access'=>'EX',
        'items'=>array(
            'Credits for'=>array(
                'access'=>'EX01',
                'url'=>'/cutIntegral/index',
            ),
            'Change list'=>array(
                'access'=>'EX02',
                'url'=>'/cut/index',
            ),
        ),
    ),
    'Search'=>array(
        'access'=>'SR',
        'items'=>array(
            'Credits search'=>array(
                'access'=>'SR01',
                'url'=>'/addSearch/index',
            ),
            'Total credit search'=>array(
                'access'=>'SR02',
                'url'=>'/sumSearch/index',
            ),
            'Exchange search'=>array(
                'access'=>'SR03',
                'url'=>'/cutSearch/index',
            ),
        ),
    ),
    'Audit'=>array(
        'access'=>'GA',
        'items'=>array(
            'Credit review'=>array(
                'access'=>'GA01',
                'url'=>'/auditAdd/index',
            ),
            'Exchange review'=>array(
                'access'=>'GA02',
                'url'=>'/auditCut/index',
            ),
        ),
    ),
    'System Setting'=>array(
        'access'=>'SS',
        'items'=>array(
            'Credit type allocation'=>array(
                'access'=>'SS01',
                'url'=>'/integralAdd/index',
            ),
            'Cut type allocation'=>array(
                'access'=>'SS04',
                'url'=>'/integralCut/index',
            ),
            'Credit activities'=>array(
                'access'=>'SS02',
                'url'=>'/activityAdd/index',
            ),
            'Cut activities'=>array(
                'access'=>'SS03',
                'url'=>'/activityCut/index',
            ),
        ),
    ),
    'Report'=>array(
        'access'=>'YB',
        'items'=>array(
            'Credits subsidiary List'=>array(
                'access'=>'YB02',
                'url'=>'/report/creditslist',
            ),
            'Credits year List'=>array(
                'access'=>'YB03',
                'url'=>'/report/yearlist',
            ),
            'Cut subsidiary List'=>array(
                'access'=>'YB04',
                'url'=>'/report/cutlist',
            ),
            'Report Manager'=>array(
                'access'=>'YB01',
                'url'=>'/queue/index',
            ),
        ),
    ),
);
