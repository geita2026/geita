<?php

namespace App\Http\Controllers\ExpoControllers;

use App\Http\Controllers\Controller;
use App\Models\ExpoParticipant;
use App\Models\setting\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\EmailNotification;
use Illuminate\Support\Facades\File;

class ExpoFrontEndController extends Controller
{
    use EmailNotification;
    public function HomePage(Request $request)
    {
        try {
            return view('ExpoPage.HomePage');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function AboutExpoPage(Request $request)
    {
        try {
            return view('ExpoPage.AboutExpo');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function TanzaniaPavilion(Request $request)
    {
        try {
            return view('ExpoPage.TanzaniaPavilion');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function ExpoPrograms(Request $request)
    {
        try {
            return view('ExpoPage.ExpoProgramms');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function ExpoRegistration(Request $request)
    {
        try {
            return view('ExpoPage.ExpoRegistration');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function SaveExpoData(Request $request)
    {
        $request->validate([
            'firstName' => 'required|max:100',
            'middleName' => 'max:100',
            'lastName' => 'required|max:100',
            'dateOfBirth' => 'date',
            'sex' => 'required|max:20',
            'passportNumber' => 'max:20|min:5|unique:expo_participants,passportNumber',
            'typeOfPassport' => 'required',
            'emailAddress' => 'email|required|unique:expo_participants,emailAddress',
            'phoneNumber' => 'required|min:10|max:13',
            'companyName' => 'required|max:100',
            'companyEmail' => 'required|max:50',
            'companyPhoneNumber' => 'required|min:10|max:13',
            'companyProfile' => 'required|max:2000',
            'typeOfParticipation' => 'required',
            'programOfInterest' => 'required',
            'anyOtherDescription' => 'max:200',
            'dateOfVisit' => 'date',
            'passportSizePhoto' => 'required|file|mimes:png,jpg,jpeg|max:2048',
            'travelPassport' => 'required|file|mimes:pdf|max:2048',
            'companySector' => 'required',
            'workingId' => 'required|file|mimes:pdf|max:2048',
            'returningDate' => 'required'
        ]);
        try {
            $participationId = Str::uuid();
            $workingId = 'working_id_' . Str::uuid() . '.' . $request->workingId->extension();
            $travelPassport = 'expo_travelPassport_' . Str::uuid() . '.' . $request->travelPassport->extension();
            $passportSizePhoto = 'expo_passPortSize_' . Str::uuid() . '.' . $request->passportSizePhoto->extension();
            $baseUrl = env('APP_URL') . '/public/uploadedFiles/';
            ExpoParticipant::create([
                'participantId' => $participationId,
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => $request->lastName,
                'dateOfBirth' => $request->dateOfBirth,
                'sex' => $request->sex,
                'passportNumber' => $request->passportNumber,
                'typeOfPassport' => $request->typeOfPassport,
                'emailAddress' => $request->emailAddress,
                'phoneNumber' => $request->phoneNumber,
                'companyName' => $request->companyName,
                'companyEmail' => $request->companyEmail,
                'companyPhoneNumber' => $request->companyPhoneNumber,
                'companyProfile' => $request->companyProfile,
                'typeOfParticipation' => $request->typeOfParticipation,
                'programOfInterest' => $request->programOfInterest,
                'anyOtherDescription' => $request->anyOtherDescription,
                'dateOfVisit' => $request->dateOfVisit,
                'passportSizePhoto' => $baseUrl . $passportSizePhoto,
                'travelPassport' => $baseUrl . $travelPassport,
                'workingId' => $baseUrl . $workingId,
                'returningDate' => $request->returningDate,
                'companySector' => $request->companySector,
            ]);
            $request->workingId->move(public_path('uploadedFiles'), $workingId);
            $request->travelPassport->move(public_path('uploadedFiles'), $travelPassport);
            $request->passportSizePhoto->move(public_path('uploadedFiles'), $passportSizePhoto);
            $emailTo = $request->emailAddress;
            $linkLable = 'view your information';
            $receiverName = $request->firstName . ' ' . $request->lastName;
            $regardsName = 'Expo 2025 Osaka Japan Organizing Committee';
            $emailSubject = 'EXPO 2025 OSAKA REGISTRATION NOTIFICATION';
            $emailLink = env('APP_URL') . '/expo2025/preview_data/' . $participationId;
            $emailContent = '
        Thank you for registering for Expo 2025 Osaka, 
        Japan! We are thrilled to confirm your 
        registration and look forward to welcoming you 
        to this amazing event. To update your submitted 
        information,please click the link below.';
            $this->sendEmail($emailTo, $linkLable, $receiverName, $emailContent, $regardsName, $emailSubject, $emailLink);
            return back()->with('success', 'Congratulations! We have received your information and interest in participating in Expo 2025 Osaka, Japan. You will be contacted soon for further details.');
        } catch (\Throwable $th) {
            return back()->with('success', 'Congratulations! We have received your information and interest in participating in Expo 2025 Osaka, Japan. You will be contacted soon for further details');
        }
    }


    public function PreviewParticipant(Request $request, $participantId)
    {
        try {
            $data = ExpoParticipant::where('participantId', $participantId)->get();
            return view('ExpoPage.PreviewInfo', compact('data'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function updateParticipant(Request $request)
    {
        try {
            $participant = ExpoParticipant::find($request->participantId);
            $workingId = $participant->workingId;
            $travelPassport = $participant->travelPassport;
            $passportSizePhoto = $participant->passportSizePhoto;
            $baseUrl = env('APP_URL') . '/public/uploadedFiles/';

            if ($request->hasFile('workingId')) {
                $file = str_replace($baseUrl, '', $workingId);
                File::delete(public_path('uploadedFiles/' . $file));
                $workingId = $baseUrl . 'working_id_' . Str::uuid() . '.' . $request->workingId->extension();
            }
            if ($request->hasFile('passportSizePhoto')) {
                $file = str_replace($baseUrl, '', $passportSizePhoto);
                File::delete(public_path('uploadedFiles/' . $file));
                $travelPassport = $baseUrl . 'expo_travelPassport_' . Str::uuid() . '.' . $request->travelPassport->extension();
            }
            if ($request->hasFile('travelPassport')) {
                $file = str_replace($baseUrl, '', $travelPassport);
                File::delete(public_path('uploadedFiles/' . $file));
                $passportSizePhoto = $baseUrl . 'expo_passPortSize_' . Str::uuid() . '.' . $request->passportSizePhoto->extension();
            }
            $participant->update([
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => $request->lastName,
                'dateOfBirth' => $request->dateOfBirth,
                'sex' => $request->sex,
                'passportNumber' => $request->passportNumber,
                'typeOfPassport' => $request->typeOfPassport,
                'emailAddress' => $request->emailAddress,
                'phoneNumber' => $request->phoneNumber,
                'companyName' => $request->companyName,
                'companyEmail' => $request->companyEmail,
                'companyPhoneNumber' => $request->companyPhoneNumber,
                'companyProfile' => $request->companyProfile,
                'typeOfParticipation' => $request->typeOfParticipation,
                'programOfInterest' => $request->programOfInterest,
                'anyOtherDescription' => $request->anyOtherDescription,
                'dateOfVisit' => $request->dateOfVisit,
                'returningDate' => $request->returningDate,
                'companySector' => $request->companySector,
                'passportSizePhoto' =>  $passportSizePhoto,
                'travelPassport' => $travelPassport,
                'workingId' => $workingId,
            ]);

            if ($request->hasFile('workingId')) {
                $request->workingId->move(public_path('uploadedFiles'), $workingId);
            }

            if ($request->hasFile('passportSizePhoto')) {
                $request->passportSizePhoto->move(public_path('uploadedFiles'), $passportSizePhoto);
            }
            if ($request->hasFile('travelPassport')) {
                $request->travelPassport->move(public_path('uploadedFiles'), $travelPassport);
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }


    public function viewRegistered(Request $request)
    {
        try {
            if ($request->secret) {
                if ($request->secret != date('dmy')) {
                    $count = session('attempts') + 1;
                    session(['attempts' => $count]);
                    $attempts = session('attempts');
                    return back()->with('fail', 'Incorrect secret code. You have attempted ' . $attempts . ' times. Please note that multiple attempts may lead to account disqualification');
                }
                session(['secret' => $request->secret]);
            }
            $total = ExpoParticipant::count();
            $expoData = ExpoParticipant::where('participantStatus', 'APPROVED')
                ->orderBy('companyName', 'ASC')->paginate(50);
            return view('ExpoPage.RegisteredTraders', compact('expoData', 'total'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    #----- Register Counterpart from Japan
    public function thirdPartRegistrationPage(Request $request)
    {
        try {
            $countries = Country::get(['country_name', 'country_short_code', 'phone_code']);
            return view('ExpoPage.ThirdPartRegistration', compact('countries'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
    public function thirdPartRegistration(Request $request)
    {
        $request->validate([
            'firstName' => 'required|max:100',
            'sex' => 'required|max:20',
            'emailAddress' => 'email|required|unique:expo_participants,emailAddress',
            'phoneNumber' => 'required|min:10|max:13',
            'companyName' => 'required|max:100',
            'companyEmail' => 'required|max:50|unique:expo_participants,companyEmail',
            'companyProfile' => 'required|max:2000',
            'anyOtherDescription' => 'max:2000',
            'companySector' => 'required',
        ]);

        try {
            $participationId = Str::uuid();

            ExpoParticipant::create([
                'participantId' => $participationId,
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => 'NULL',
                'sex' => $request->sex,
                'emailAddress' => $request->emailAddress,
                'phoneNumber' => $request->phoneNumber,
                'companyName' => $request->companyName,
                'companyEmail' => $request->companyEmail,
                'country' => $request->country,
                'companyProfile' => $request->companyProfile,
                'programOfInterest' => 'Tanzania Business, Investment and Tourism Forum [ 26th May,
                                                            2025 ]',
                'typeOfParticipation' => 'Program Maalumu Siku ya Business Forum',
                'anyOtherDescription' => $request->anyOtherDescription,
                'companySector' => $request->companySector,
                'participantStatus' => 'UNAPPROVED',
                'dateOfBirth' => 'NULL',
                'passportNumber' => 'NULL',
                'typeOfPassport' => 'NULL',
                'companyPhoneNumber' => 'NULL',
                'dateOfVisit' => 'NULL',
                'passportSizePhoto' => 'NULL',
                'travelPassport' => 'NULL'
            ]);
            $emailTo = $request->emailAddress;
            $linkLable = null;
            $receiverName = $request->firstName . ' ' . $request->lastName;
            $regardsName = 'Expo 2025 Osaka Japan Organizing Committee';
            $emailSubject = 'EXPO 2025 OSAKA REGISTRATION NOTIFICATION';
            $emailLink = null;
            $emailContent = "
            Thank you for registering with TanTrade for Expo
             2025 Osaka! We invite you to explore Tanzania's 
             vibrant business opportunities and investment 
             potential. Join us in fostering global
             connections and driving innovation together!
        ";
            $this->sendEmail($emailTo, $linkLable, $receiverName, $emailContent, $regardsName, $emailSubject, $emailLink);
            return back()->with('success', 'Congratulations! We have received your information on interest in investing and making Business with Tanzanians. You will be contacted soon for further details.');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }
}
