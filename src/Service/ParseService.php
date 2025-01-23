<?php

namespace App\Service;

use App\Models\AdsAdset;
use App\Models\AdsCampaign;
use App\Models\Ads;
use Illuminate\Database\Eloquent\Collection;

class ParseService
{
    public static function processCampaign(
        array $readableColumns,
        array $existingCampaignIds,
        array $newCampaignData,
        array $row
    ): array
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

    public static function processTargetingGroup(
        array $readableColumns,
        array $existingTargetingGroupIds,
        array $newTargetingGroupData,
        array $row
    ): array
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

    public static function handleMassiveInsertions(
        array      $newCampaignData,
        array      $newTargetingGroupData,
        Collection $adsCollection
    ): void
    {
        if (!empty($newCampaignData)) {
            AdsCampaign::upsert(array_values($newCampaignData), ['id']);
            print_r("Upserted new campaigns:\n");
            print_r(array_values($newCampaignData));
        }

        if (!empty($newTargetingGroupData)) {
            AdsAdset::upsert(array_values($newTargetingGroupData), ['id']);
            print_r("Upserted new targeting groups:\n");
            print_r(array_values($newTargetingGroupData));
        }

        if (!$adsCollection->isEmpty()) {
            Ads::upsert(
                $adsCollection->toArray(),
                ['title', 'spending_amount', 'reference_ad_id'],
                ['impression_count', 'click_count']
            );
            print_r("Upserted new ads data:\n");
            print_r($adsCollection->toArray());
        }
    }

    public static function handleSingleInsertions(
        array      $newCampaignData,
        array      $newTargetingGroupData,
        Collection $adsCollection
    ): void
    {
        foreach ($newCampaignData as $campaign) {
            AdsCampaign::updateOrCreate(
                ['title' => $campaign['title']],
                $campaign
            );
            print_r("Inserted/Updated campaign: {$campaign['title']}\n");
        }

        foreach ($newTargetingGroupData as $targetingGroup) {
            AdsAdset::updateOrCreate(
                ['title' => $targetingGroup['title']],
                $targetingGroup
            );
            print_r("Inserted/Updated targeting group: {$targetingGroup['title']}\n");
        }

        foreach ($adsCollection as $ad) {
            Ads::updateOrCreate(
                ['title' => $ad->title],
                $ad->toArray()
            );
            print_r("Inserted/Updated ad: {$ad->title}\n");
        }
    }

    public static function columnNames($documentRow): array
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
