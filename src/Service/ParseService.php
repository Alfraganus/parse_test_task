<?php
namespace App\Service;

use App\Models\AdsAdset;
use App\Models\AdsCampaign;
use App\Models\Ads;
use Illuminate\Database\Eloquent\Collection;

class ParseService
{
    public static function processCampaign(array $readableColumns, array $existingCampaignIds, array $newCampaignData, array $row): array
    {
        if (!isset($existingCampaignIds[$readableColumns['campaign_identifier']])) {
            $newCampaignData[$readableColumns['campaign_identifier']] = [
                'id' => $readableColumns['campaign_identifier'],
                'title' => $row[5],
            ];
             print_r("New campaign detected, adding: {$readableColumns['campaign_identifier']}");
        }

        return $newCampaignData;
    }

    public static function processTargetingGroup(array $readableColumns, array $existingTargetingGroupIds, array $newTargetingGroupData, array $row): array
    {
        if (!isset($existingTargetingGroupIds[$readableColumns['targeting_group_id']])) {
            $newTargetingGroupData[$readableColumns['targeting_group_id']] = [
                'id' => $readableColumns['targeting_group_id'],
                'title' => $row[7],
            ];
             print_r("New targeting group detected, adding: {$readableColumns['targeting_group_id']}");
        }

        return $newTargetingGroupData;
    }

    public static function handleMassiveInsertions(array $newCampaignData, array $newTargetingGroupData, Collection $adsCollection): void
    {
        if (!empty($newCampaignData)) {
            AdsCampaign::upsert(array_values($newCampaignData), ['id']);
             print_r("Upserted new campaigns.");
        }

        if (!empty($newTargetingGroupData)) {
            AdsAdset::upsert(array_values($newTargetingGroupData), ['id']);
             print_r("Upserted new targeting groups.");
        }

        if (!$adsCollection->isEmpty()) {
            Ads::upsert($adsCollection->toArray(), ['id']);
             print_r("Upserted new ads.");
        }
    }

    public static function handleSingleInsertions(array $newCampaignData, array $newTargetingGroupData, Collection $adsCollection): void
    {
        foreach ($newCampaignData as $campaign) {
            AdsCampaign::updateOrCreate(['id' => $campaign['id']], $campaign);
             print_r("Inserted/Updated campaign: {$campaign['id']}");
        }

        foreach ($newTargetingGroupData as $targetingGroup) {
            AdsAdset::updateOrCreate(['id' => $targetingGroup['id']], $targetingGroup);
             print_r("Inserted/Updated targeting group: {$targetingGroup['id']}");
        }

        foreach ($adsCollection as $ad) {
            Ads::updateOrCreate(['id' => $ad->id], $ad->toArray());
             print_r("Inserted/Updated ad: {$ad->id}");
        }
    }

    public static function columnNames($documentRow)
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
}
