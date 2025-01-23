<?php

namespace App\Models;

use App\Service\ParseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class Ads extends Model
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

    public static function parse(SimpleXLSX $file, $fileProcessingMode)
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
        Ads::truncate();
        foreach ($dataRows as $row) {
            $readableColumns = ParseService::columnNames($row);
            $adsCollection->push(new self($readableColumns));
            $newCampaignData = ParseService::processCampaign(
                $readableColumns,
                $existingCampaignIds,
                $newCampaignData,
                $row
            );
            $newTargetingGroupData = ParseService::processTargetingGroup(
                $readableColumns,
                $existingTargetingGroupIds,
                $newTargetingGroupData,
                $row
            );
        }
        $fileProcessingMode
            ? ParseService::handleMassiveInsertions($newCampaignData, $newTargetingGroupData, $adsCollection)
            : ParseService::handleSingleInsertions($newCampaignData, $newTargetingGroupData, $adsCollection);

    }

}
