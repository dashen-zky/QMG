<?php
namespace backend\modules\fin\stamp\models;
use backend\models\UUID;
use Yii;
use yii\db\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:11
 */
class ImportStamp extends Stamp
{
    public function importStampList($condition = null) {
        $stampList = (!empty($condition))?$this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            [
                'and',
                $condition,
                [
                    '=',
                    $this->aliasMap['stamp'].'.type',
                    StampConfig::ImportStamp,
                ]
            ]
        ):$this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            [
                '=',
                $this->aliasMap['stamp'].'.type',
                StampConfig::ImportStamp,
            ]
        );
        return $stampList;
    }

    public function listFilter($filter) {
        $entrance = isset($filter['entrance'])?$filter['entrance']:false;
        unset($filter['entrance']);
        if(empty($filter)) {
            $condition = ($entrance == StampConfig::SelectEntrance)?[
                '=',
                $this->aliasMap['stamp'] . '.enable',
                StampConfig::Enable,
            ]:null;
            return $this->importStampList($condition);
        }

        $this->handlerFormDataTime($filter, 'min_made_time');
        $this->handlerFormDataTime($filter, 'max_made_time');

        $condition = [
            'and',
            [
                '=',
                $this->aliasMap['stamp'].'.type',
                StampConfig::ImportStamp,
            ],
        ];

        if($entrance == StampConfig::SelectEntrance) {
            $condition[] = [
                '=',
                $this->aliasMap['stamp'] . '.enable',
                StampConfig::Enable,
            ];
        }

        $map = [
            'series_number'=>[
                'like',
                'stamp',
                'series_number',
            ],
            'service_type'=>[
                '=',
                'stamp',
                'service_type'
            ],
            'status'=>[
                '=',
                'stamp',
                'status',
            ],
            'min_money'=>[
                '>=',
                'stamp',
                'money',
            ],
            'max_money'=>[
                '<=',
                'stamp',
                'money',
            ],
            'min_made_time'=>[
                '>=',
                'stamp',
                'made_time',
            ],
            'max_made_time'=>[
                '<=',
                'stamp',
                'made_time',
            ],
            'provider'=>[
                'like',
                'stamp',
                'provider',
            ],
            'receiver'=>[
                'like',
                'stamp',
                'receiver',
            ],
        ];

        foreach ($filter as $index => $item) {
            $condition[] = [
                $map[$index][0],
                $this->aliasMap[$map[$index][1]] . '.' . $map[$index][2],
                $item
            ];
        }

        return $this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            $condition
        );
    }
    
    public function insertStamp($formData) {
        if(empty($formData)) {
            return true;
        }

        $seriesNumber = $this->seriesNumberHandler($formData['series_number']);
        if($seriesNumber === self::SeriesValidateError) {
            return self::SeriesValidateError;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 同一次提交的submit_uuid是一致的
            $formData['submit_uuid'] = UUID::getUUID();
            $formData['type'] = StampConfig::ImportStamp;
            $formData['status'] = StampConfig::Unchecked;
            $formData['enable'] = StampConfig::Enable;
            foreach ($seriesNumber as $item) {
                $formData['uuid'] = UUID::getUUID();
                $formData['series_number'] = $item;
                if($this->insertRecord($formData) === self::SeriesValidateError) {
                    return self::SeriesValidateError;
                }
                // 附件只要上传一次即可
                unset($formData['attachment']);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    // 处理发票编号的函数
    // 0000,000，0001-100
    protected function seriesNumberHandler($seriesNumberString) {
        $seriesNumbers = explode(',', trim($seriesNumberString,',\s'));
        $seriesNumber = [];
        foreach ($seriesNumbers as $item) {
            $item = trim($item);
            $errorReg = '/[^\w\~\-\@]/';
            if(preg_match($errorReg, $item, $errorMatch)) {
                return self::SeriesValidateError;
            }

            $reg = '/(\d+)\s*[\-\@\~]+\s*(\d+)/';
            if(!preg_match($reg, $item, $match)) {
                $seriesNumber[] = $item;
                continue;
            }

            $matchLen = strlen($match[2]);
            $len = strlen($match[1]);
            $min = substr($match[1], -$matchLen);
            $max = $match[2];
            $prefix = substr($match[1], 0, $len - $matchLen);
            if($max < $min) {
                return self::SeriesValidateError;
            }

            while($min <= $max) {
                $minLen = strlen($min);
                $zeroPlaceholder = '';
                while ($matchLen - $minLen > 0) {
                    $zeroPlaceholder .= '0';
                    $minLen++;
                }
                $seriesNumber[] = $prefix . $zeroPlaceholder . $min;
                $min++;
            }
        }
        return $seriesNumber;
    }
}