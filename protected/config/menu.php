<?php

return array(
    'Credit'=>array(
        'access'=>'DE',
		'icon'=>'fa-flag-checkered',
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
		'icon'=>'fa-diamond',
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
		'icon'=>'fa-binoculars',
        'items'=>array(
            'Credits search'=>array(
                'access'=>'SR01',
                'url'=>'/creditSearch/index',
            ),
            'Total credit search'=>array(
                'access'=>'SR02',
                'url'=>'/sumSearch/index',
            ),
            'Credit deduction details'=>array(
                'access'=>'SR06',
                'url'=>'/deductionSearch/index',
            ),
            'Exchange search'=>array(
                'access'=>'SR03',
                'url'=>'/giftSearch/index',
            ),
            'Sum Gift search'=>array(
                'access'=>'SR05',
                'url'=>'/SumGiftSearch/index',
            ),
            'Prize search'=>array(
                'access'=>'SR07',
                'url'=>'/prizeSearch/index',
            ),
            'Stretch search'=>array(
                'access'=>'SR04',
                'url'=>'/StretchSearch/index',
            ),
        ),
    ),
    'ranking list'=>array(
        'access'=>'RL',
		'icon'=>'fa-sort-numeric-asc',
        'items'=>array(
            'City ranking'=>array(
                'access'=>'RL01',
                'url'=>'/rankCity/index',
            ),
            'National ranking'=>array(
                'access'=>'RL02',
                'url'=>'/rankNational/index',
            ),
            'Dezhi body group beauty Top20'=>array(
                'access'=>'RL03',
                'url'=>'/rankGroup/index',
            ),
            'Valid ranking'=>array(
                'access'=>'RL04',
                'url'=>'/rankValid/index',
            ),
        ),
    ),
    'Audit'=>array(
        'access'=>'GA',
		'icon'=>'fa-legal',
        'items'=>array(
            'Confirm review'=>array(
                'access'=>'GA04',
                'url'=>'/confirmCredit/index',
            ),
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
		'icon'=>'fa-gear',
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
		'icon'=>'fa-file-text-o',
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
            'Prize List Report'=>array(
                'access'=>'YB05',
                'url'=>'/report/prizelist',
            ),
            'Stretch List Report'=>array(
                'access'=>'YB06',
                'url'=>'/report/stretchlist',
            ),
            'Sum Gift Report'=>array(
                'access'=>'YB07',
                'url'=>'/report/giftlist',
            ),
            'Report Manager'=>array(
                'access'=>'YB01',
                'url'=>'/queue/index',
            ),
        ),
    ),
);
