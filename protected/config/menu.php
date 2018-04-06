<?php

return array(
    'Data Entry'=>array(
        'access'=>'DE',
        'items'=>array(
            'Credit application'=>array(
                'access'=>'DE01',
                'url'=>'/integral/new',
            ),
            'Apply list'=>array(
                'access'=>'DE02',
                'url'=>'/integral/index',
            ),
            'Change list'=>array(
                'access'=>'DE03',
                'url'=>'/cut/index',
            ),
        ),
    ),
    'Exchange'=>array(
        'access'=>'EX',
        'items'=>array(
            'Credits for'=>array(
                'access'=>'EX01',
                'url'=>'/integralCut/index',
            ),
        ),
    ),
    'Search'=>array(
        'access'=>'SR',
        'items'=>array(
            'Credits search'=>array(
                'access'=>'SR01',
                'url'=>'/integralSearch/index',
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
                'url'=>'/auditIntegral/add',
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
        ),
    ),
    /*
        'Report'=>array(
            'access'=>'ZY',
            'items'=>array(
                'Staff List'=>array(
                    'access'=>'ZB01',
                    'url'=>'#',
                    'hidden'=>true,
                ),
            ),
        ),*/
);
