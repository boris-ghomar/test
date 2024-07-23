<?php

namespace App\Http\Controllers\Site\CustomPages;

use App\Enums\Settings\DynamicDataVariablesEnum;
use App\Http\Controllers\Controller;
use App\Models\BackOffice\Settings\DynamicData;
use Illuminate\Http\Request;

class IpRestrictionController extends Controller
{
    /**
     * index
     *
     */
    public function index()
    {
        return view('hhh.Site.pages.CustomPages.IpRestriction.CloudFlairPage.index');
    }

    /**
     * Show site link
     *
     * @return void
     */
    public function showSiteLink()
    {
        $explanation = DynamicData::get(DynamicDataVariablesEnum::IpRestriction_Explanation, __('thisApp.CustomPages.IpRestriction.UnsupportedIP'));
        $explanation = str_replace("\n", "<br/>", $explanation);

        $data = [
            'explanation' => $explanation,
            'siteLink' => DynamicData::get(DynamicDataVariablesEnum::IpRestriction_SiteLink),
        ];

        return view('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.index', $data);
    }
}
