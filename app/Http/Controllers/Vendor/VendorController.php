<?php

    namespace App\Http\Controllers\Vendor;

    use App\Http\Controllers\Controller;
    use App\Models\BusinessLink;
    use App\Models\User;
    use App\Models\UserData;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;

    use Illuminate\Support\Str;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    class VendorController extends Controller
    {
        public function __construct()
        {
            $this->middleware('vendorCheck', ['except' => ['login', 'register']]);
        }

        public function index(Request $request)
        {
            $user = User::where('userid', authUser()->userid)->with(['user_data.business_links', 'food.category'])->get();
            return success('Vendor Information ', $user, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function editProfile(Request $request)
        {
            $profile = User::find($request->user()->id);
            return success('Profile information fetched', $profile, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function updateProfile(Request $request)
        {
            $profile = User::find($request->user()->id);
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->phone_number = $request->phone_number;
            $profile->email = $request->email;
            $profile->update();
            return success('Profile information updated', $profile, 200);
        }


        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function setBusinessInfo(Request $request)
        {
            $this->validate($request, config('validator.business_info'));

            $checkBusinessInfo = UserData::where('business_name', $request->business_name)->exists();
            if (!$checkBusinessInfo) {
                $userData = new UserData();
                $userData->uid = authUser()->id;
                $userData->userid = authUser()->userid;
                $userData->business_name = $request->business_name;
                $userData->business_address = $request->business_address;
                $userData->city_id = $request->city_id;
                $userData->state_id = $request->state_id;
                $userData->country_id = $request->country_id;
                $userData->save();

                $userData = new BusinessLink();
                $userData->uid = authUser()->id;
                $userData->userid = authUser()->userid;
                $userData->business_name = $request->business_name;
                $userData->business_link = Str::slug($request->business_name);
                $userData->business_qr = QrCode::generate('http://localhost:8000/qr/' . Str::slug($request->business_name));
                $userData->save();

                return success('Business data created successfully. ', $userData, 200);
            }
            return error('Business data already exist. ', [], 400);
        }

        public function setBusinessLink(Request $request)
        {
            $this->validate($request, config('validator.set_business_name'));

            $checkBusinessLink = BusinessLink::where('business_name', $request->business_name)->exists();
            if (!$checkBusinessLink && $checkBusinessLink != $request->business_name) {
                $userData = new BusinessLink();
                $userData->uid = authUser()->id;
                $userData->userid = authUser()->userid;
                $userData->business_name = $checkBusinessLink;
                $userData->business_link = Str::slug($request->business_name);
                $userData->business_qr = QrCode::generate('http://localhost:8000/qr/' . Str::slug($request->business_name));
                $userData->save();
                return success('Business link created successful. ', $userData, 200);
            }
            return error('Business link already exist, try another link. ', [], 400);
        }
    }
