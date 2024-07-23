<?php

namespace App\Http\Controllers\BackOffice;

use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\GendersEnum;
use App\HHH_Library\general\php\traits\Enums\EnumSession;
use App\Models\BackOffice\PersonnelProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\PersonnelProfileRequest;
use App\Models\BackOffice\Settings\Setting;
use App\Models\General\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonnelProfileController extends Controller
{
    use EnumSession;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonnelProfile $personnelProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\PersonnelProfile  $personnelProfile
     * @return view
     */
    public function edit(PersonnelProfile $personnelProfile)
    {
        $userProfile = PersonnelProfile::first();

        $genderCollection = DropdownListCreater::makeByArray(GendersEnum::translatedArray())
            ->sort(true)->get();

        $data = [
            'AdminPublicRoutesEnum' => AdminPublicRoutesEnum::class,
            'UsersTableEnum' => UsersTableEnum::class,
            'PersonnelExtrasTableEnum' => PersonnelExtrasTableEnum::class,
            'userProfile' => $userProfile,
            'userProfileExtra' => $userProfile->personnelExtra,
            'GenderCollection' => $genderCollection,
        ];

        $data = array_merge($data, $this->getUserSettings());

        return view('hhh.BackOffice.pages.UserProfile.edit.index', $data);
    }

    /**
     * Get user settings data for settings tab
     *
     * @return array
     */
    private function getUserSettings(): array
    {
        // Settings & Default values
        $case = AppSettingsEnum::AdminPanelTimeZone;
        $setting[$case->name] = UserSetting::get($case, "");
        $defaults[$case->name] = Setting::get($case);

        $case = AppSettingsEnum::AdminPanelCalendarType;
        $setting[$case->name] = UserSetting::get($case, "");
        $defaults[$case->name] = constant(CalendarTypeEnum::class . '::' . Setting::get($case))->translate();

        $setting = json_decode(json_encode($setting), false);
        $defaults = json_decode(json_encode($defaults), false);

        // Permissions for change items
        $canPersonnelChangeTimeZone = Setting::get(AppSettingsEnum::canPersonnelChangeTimeZone);
        $canPersonnelChangeCalendarType = Setting::get(AppSettingsEnum::canPersonnelChangeCalendarType);

        $calendarTypeDropdown = DropdownListCreater::makeByArray(CalendarTypeEnum::translatedArray())
            ->prepend(__('general.SystemDefault'), "")->sort(true)->get();

        return [

            'AppSettingsEnum' => AppSettingsEnum::class,
            'settingsTabDispaly' => $canPersonnelChangeTimeZone || $canPersonnelChangeCalendarType,
            'canPersonnelChangeTimeZone' => $canPersonnelChangeTimeZone,
            'canPersonnelChangeCalendarType' => $canPersonnelChangeCalendarType,
            'calendarTypeDropdown' => $calendarTypeDropdown,
            'setting' => $setting,
            'defaults' => $defaults,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\PersonnelProfileRequest  $request
     * @param  \App\Models\BackOffice\PersonnelProfile  $personnelProfile
     * @return view
     */
    public function update(PersonnelProfileRequest $request, PersonnelProfile $personnelProfile)
    {
        try {

            /** @var PersonnelProfile $personnelProfile */
            $personnelProfile = PersonnelProfile::find(auth()->user()->id);

            $tabpanel = $request->_tabpanel;

            if ($tabpanel == "Personal") {

                $personnelProfile[UsersTableEnum::Email->dbName()] = $request->input(UsersTableEnum::Email->dbName());
                $personnelProfile->save();

                $personnelExtra = $personnelProfile->personnelExtra;
                $personnelExtra->fill($request->all());
                $personnelExtra->save();
            } else if ($tabpanel == "Photo") {

                /************** File ******************/
                $photoField = UsersTableEnum::ProfilePhotoName->dbName();
                $lastFile = $personnelProfile->getPhotoFileAssistant(false);
                $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

                if ($storedFileName != null)
                    $personnelProfile[$photoField] = $storedFileName;
                /************** File END ******************/

                $personnelProfile->save();
            } else if ($tabpanel == "Password") {

                // New Password
                if (!empty($request->input('new_password'))) {
                    $personnelProfile[UsersTableEnum::Password->dbName()] = Hash::make($request->input('new_password'));
                    $personnelProfile->save();

                    EnumSession::logoutUserFromAllDevices($personnelProfile);
                }


            } else if ($tabpanel == "Settings") {

                $case = AppSettingsEnum::AdminPanelTimeZone;
                UserSetting::saveItem($case, $request->input($case->name));

                $case = AppSettingsEnum::AdminPanelCalendarType;
                UserSetting::saveItem($case, $request->input($case->name));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->withErrors([$th->getMessage()]);
        }

        return redirect()->back()
            ->with('success', trans('PagesContent_PersonnelProfile.messages.SavedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonnelProfile $personnelProfile)
    {
        //
    }
}
