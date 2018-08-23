<?php

return array(
    'Credit'=>array(
        'access'=>'DE',
        'items'=>array(
            'Credit application'=>array(
                'access'=>'DE01',
                'url'=>'/creditRequest/new',
            ),
            'Credit Apply list'=>array(
                'access'=>'DE02',
                'url'=>'/creditRequest/index',
            ),
            'Credit exchange prize'=>array(
                'access'=>'DE03',
                'url'=>'/prizeRequest/index',
            ),
        ),
    ),
    'Exchange'=>array(
        'access'=>'EX',
        'items'=>array(
            'Credits for'=>array(
                'access'=>'EX01',
                'url'=>'/gift/index',
            ),
            'Change list'=>array(
                'access'=>'EX02',
                'url'=>'/giftRequest/index',
            ),
        ),
    ),
    'Search'=>array(
        'access'=>'SR',
        'items'=>array(
            'Credits search'=>array(
                'access'=>'SR01',
                'url'=>'/creditSearch/index',
            ),
            'Total credit search'=>array(
                'access'=>'SR02',
                'url'=>'/sumSearch/index',
            ),
            'Exchange search'=>array(
                'access'=>'SR03',
                'url'=>'/giftSearch/index',
            ),
        ),
    ),
    'ranking list'=>array(
        'access'=>'RL',
        'items'=>array(
            'City ranking'=>array(
                'access'=>'RL01',
                'url'=>'/rankCity/index',
            ),
            'National ranking'=>array(
                'access'=>'RL02',
                'url'=>'/rankNational/index',
            ),
        ),
    ),
    'Audit'=>array(
        'access'=>'GA',
        'items'=>array(
            'Credit review'=>array(
                'access'=>'GA01',
                'url'=>'/auditCredit/index',
            ),
            'Exchange review'=>array(
                'access'=>'GA02',
                'url'=>'/auditGift/index',
            ),
            'Prize review'=>array(
                'access'=>'GA03',
                'url'=>'/auditPrize/index',
            ),
        ),
    ),
    'System Setting'=>array(
        'access'=>'SS',
        'items'=>array(
            'Credit type allocation'=>array(
                'access'=>'SS01',
                'url'=>'/creditType/index',
            ),
            'Cut type allocation'=>array(
                'access'=>'SS04',
                'url'=>'/giftType/index',
            ),
            'Prize Type'=>array(
                'access'=>'SS02',
                'url'=>'/prizeType/index',
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
