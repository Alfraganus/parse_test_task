<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Shuchkin\SimpleXLSX;
use Slim\Logger;
use Slim\Psr7\Response;

class AdsWorking extends Model
{
    protected $table = 'ads';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'record_date',
        'spending_amount',
        'reference_ad_id',
        'advertisement_name',
        'campaign_identifier',
        'targeting_group_id',
        'impression_count',
        'click_count',
    ];

    public static function parse(SimpleXLSX $file)
    {
        $rows = $file->rows();

        if (empty($rows)) {
            return;
        }
        $dataRows = array_slice($rows, 1);

        $existingCampaignIds = array_flip(AdsCampaign::pluck('id')->all());
        $existingTargetingGroupIds = array_flip(AdsAdset::pluck('id')->all());

        $adsCollection = new Collection();
        $newCampaignData = [];
        $newTargetingGroupData = [];
        foreach ($dataRows as $row) {
            $readableColumns = self::columnNames($row);

            $adsCollection->push(new self($readableColumns));

            if (!isset($existingCampaignIds[$readableColumns['campaign_identifier']])) {
                $newCampaignData[$readableColumns['campaign_identifier']] = [
                    'id' => $readableColumns['campaign_identifier'],
                    'title' => $row[5],
                ];
                print_r("New campaign detected, adding: {$readableColumns['campaign_identifier']}") ;

            }

            if (!isset($existingTargetingGroupIds[$readableColumns['targeting_group_id']])) {
                $newTargetingGroupData[$readableColumns['targeting_group_id']] = [
                    'id' => $readableColumns['targeting_group_id'],
                    'title' => $row[7],
                ];
                print_r("New targeting group detected, adding: {$readableColumns['targeting_group_id']}") ;
            }
        }

        if (!empty($newCampaignData)) {
            AdsCampaign::upsert(array_values($newCampaignData), ['id']);
            print_r("Upserted new campaigns.") ;
            print_r($newCampaignData) ;
        }

        if (!empty($newTargetingGroupData)) {
            AdsAdset::upsert(array_values($newTargetingGroupData), ['id']);
            print_r("Upserted new targeting groups") ;
            print_r($newTargetingGroupData) ;
        }
        self::upsert($adsCollection->toArray(), ['id']);

    }

    private static function columnNames($documentRow)
    {
        return [
            'record_date' => $documentRow[0],
            'spending_amount' => $documentRow[1],
            'reference_ad_id' => (int)$documentRow[2],
            'advertisement_name' => $documentRow[3],
            'campaign_identifier' => $documentRow[4],
            'targeting_group_id' => $documentRow[6],
            'impression_count' => $documentRow[8],
            'click_count' => $documentRow[9],
        ];
    }

    public static function addSingle(array $data)
    {
        return self::create($data);
    }

    public static function addMany(array $data)
    {
        return self::insert($data);
    }

    public static function updateRecord(int $id, array $data)
    {
        $ad = self::find($id);
        if ($ad) {
            $ad->update($data);
            return $ad;
        }
        return null;
    }

    public static function deleteRecord(int $id)
    {
        $ad = self::find($id);
        if ($ad) {
            $ad->delete();
            return true;
        }
        return false;
    }
}
