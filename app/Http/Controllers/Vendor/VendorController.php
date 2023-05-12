<?php

    namespace App\Http\Controllers\Vendor;

    use App\Http\Controllers\CloudinaryStorage;
    use App\Http\Controllers\Controller;
    use App\Models\BusinessLink;
    use App\Models\User;
    use App\Models\UserData;
    use App\Models\VendorMedia;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Hash;

    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Psy\Util\Json;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use function PHPUnit\Framework\isEmpty;

    class VendorController extends Controller
    {
        public function __construct()
        {
            $this->middleware('vendorCheck', ['except' => ['login', 'register']]);
        }

        public function index()
        {
            $user = User::where('userid', authUser()->userid)->with(['user_data.business_links', 'food.category'])->get();
            return success('Vendor Information ', $user, Response::HTTP_OK);
        }


        public function profile(): JsonResponse
        {
            try {
                $user = User::where('userid', authUser()->userid)->with('vendor_media')->get();
                return success('Vendor Information ', $user, Response::HTTP_OK);
            } catch (\Exception $exception) {
                Log::debug('Profile fetch exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Error fetching Profile, please try again. ', [], 400);
        }

        /**
         * Edit the Authenticated User profile.
         *
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function editProfile(Request $request): JsonResponse
        {
            try {
                $profile = User::find(authUser()->id);
                return success('Profile information fetched', $profile, Response::HTTP_OK);
            } catch (\Exception $exception) {
                Log::debug('Edit profile fetch exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Error getting profile, please try again. ', [], Response::HTTP_BAD_REQUEST);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateProfile(Request $request): JsonResponse
        {
            try {
                $profile = User::find(authUser()->id);
                $profile->first_name = $request->query('first_name');
                $profile->last_name = $request->query('last_name');
                $profile->phone_number = $request->query('phone_number');
                $profile->email = $request->query('email');
                $profile->update();
                return success('Profile information updated', $profile, Response::HTTP_OK);

            } catch (\Exception $exception) {
                Log::debug('Profile update exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Error updating profile, Please  try again. ', [], Response::HTTP_BAD_REQUEST);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function setBusinessInfo(Request $request): JsonResponse
        {
            try {

                $validated_data = $this->validate($request, config('validation.business_info'));

                $checkBusinessInfo = UserData::where('business_name', $validated_data['business_name'])->exists();
                if (!$checkBusinessInfo) {
                    $userData = new UserData();
                    $userData->uid = authUser()->id;
                    $userData->userid = authUser()->userid;
                    $userData->business_name = $validated_data['business_name'];
                    $userData->business_address = $validated_data['business_address'];
                    $userData->city_id = $validated_data['city_id'];
                    $userData->state_id = $validated_data['state_id'];
                    $userData->country_id = $validated_data['country_id'];
                    $userData->save();

                    $userData = new BusinessLink();
                    $userData->uid = authUser()->id;
                    $userData->userid = authUser()->userid;
                    $userData->business_name = $validated_data['business_name'];
                    $userData->business_link = Str::slug($validated_data['business_name']);
                    $userData->business_qr = QrCode::generate(config('env.base_url.qr_url') . Str::slug($validated_data['business_name']));
                    $userData->save();

                    return success('Business data created successfully. ', $userData, Response::HTTP_OK);
                }
                return error('Business data already exist. ', [], 400);
            } catch (\Exception $exception) {
                Log::debug('Set Business Name exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Error creating Business Data. ', [], Response::HTTP_BAD_REQUEST);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function setBusinessLink(Request $request): JsonResponse
        {
            try {
                $validated_data = $this->validate($request, config('validation.set_business_name'));

                $checkBusinessLink = BusinessLink::where('business_name', $validated_data['business_name'])->exists();
                if (!$checkBusinessLink && $checkBusinessLink != $validated_data['business_name']) {
                    $userData = new BusinessLink();
                    $userData->uid = authUser()->id;
                    $userData->userid = authUser()->userid;
                    $userData->business_name = $validated_data['business_name'];
                    $userData->business_link = Str::slug($validated_data['business_name']);
                    $userData->business_qr = QrCode::generate('http://localhost:8000/qr/' . Str::slug($validated_data['business_name']));
                    $userData->save();
                    return success('Business link created successful. ', $userData, Response::HTTP_OK);
                }
                return error('Business link already exist, try another link. ', [], 400);
            } catch (\Exception $exception) {
                Log::debug('Set Business Link exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Error creating Business link, try again. ', [], Response::HTTP_BAD_REQUEST);
        }

        public function setMedia(Request $request): JsonResponse
        {
            $logo_file = $request->file('logo_file');
            $hero_file = $request->file('hero_file');
            try {

                if (!empty([$logo_file || $hero_file])) {
                    switch ($logo_file || $hero_file) {
                        case isset($logo_file) && isset($hero_file):
                            $logo_data = CloudinaryStorage::upload($logo_file->getRealPath(), $logo_file->getClientOriginalName());
                            $hero_data = CloudinaryStorage::upload($hero_file->getRealPath(), $hero_file->getClientOriginalName());

                            // Store file reference in database

                            $data = VendorMedia::updateOrCreate(
                                ['vendor_id' => authUser()->id],
                                ['logo' => $logo_data, 'hero' => $hero_data]
                            );
                            return success('Business logo and hero created successful. ', $data, Response::HTTP_CREATED);
                            break;

                        case isset($logo_file):
                            $logo_data = CloudinaryStorage::upload($logo_file->getRealPath(), $logo_file->getClientOriginalName());

                            Log::debug($logo_data);

                            // Store file reference in database
                            $data = VendorMedia::updateOrCreate(
                                ['vendor_id' => authUser()->id],
                                ['logo' => $logo_data]
                            );
                            return success('Business logo created successful. ', $data, Response::HTTP_CREATED);
                            break;

                        case isset($hero_file):
                            $hero_data = CloudinaryStorage::upload($hero_file->getRealPath(), $hero_file->getClientOriginalName());

                            // Store file reference in database
                            $data = VendorMedia::updateOrCreate(
                                ['vendor_id' => authUser()->id],
                                ['hero' => $hero_data]
                            );
                            return success('Business hero created successful. ', $data, Response::HTTP_CREATED);
                            break;

                        default:
                            return error('Something went wrong while uploading. ', [], Response::HTTP_BAD_REQUEST);
                    }
                }

            } catch (\Exception $exception) {
                Log::debug('Set Business Media exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
            }
            return error('Files are empty or not supported. ', [], Response::HTTP_BAD_REQUEST);
        }

    }
