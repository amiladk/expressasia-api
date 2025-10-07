<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Mail; 

//models
use App\Models\User;
use App\Models\Rider;
use App\Models\City;
use App\Models\Bank;
use App\Models\Client;
use App\Models\Package;
use App\Models\PackageType;
use App\Models\PackagePhone;
use App\Models\PackageHistory;
use App\Models\DifferentPickupAddress;
use App\Models\ClientPricing;
use App\Models\ClientSettlementBatch;
use App\Models\ClientSettlementItem;

class ApiController extends DataController
{
  
    /*
    |--------------------------------------------------------------------------
    |Public function / Create Packge 
    |--------------------------------------------------------------------------
    */
    public function createPackge(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }

            $city = City::has('BranchCoverage')->where('city',$request->city)->first();
            if($city){
                $request->merge(['city' => $city->id]);
            }else{
                $request->merge(['city' => NULL]);
            }
            
            $packageType = PackageType::where('package_type',$request->package_type)->first();
            if($packageType){
                $request->merge(['package_type' => $packageType->id]);
            }else{
                $request->merge(['package_type' => NULL]);
            }

            $validation_array = [
                'api_key'          => 'required',
                'waybill'          => 'required|unique:package',
                'city'             => 'required',
                'client_ref'       => 'required',
                'recipient'        => 'required',
                'address'          => 'required',
                'cod'              => 'required|numeric',
                'package_type'     => 'required',
                'client_remarks'   => 'nullable',
                // 'weight'           => 'required|numeric|not_in:0',
                'item_description' => 'nullable',
                'phone.*'          => 'nullable|digits:10|starts_with:0',
                'phone.0'          => 'digits:10|starts_with:0|required',
              
            ];

            $customMessages = [
                'city.required'      => 'The city field is incorrect.',
                'api_key.required'   => 'api key required or invalid'
            ];
            
            $validator = Validator::make($request->all(), $validation_array, $customMessages);

            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            DB::beginTransaction();

            // $package = Package::create(array_merge(
            //     $validator->validated(),           
            //     ['status' => 1,
            //     'client' => $client->id]           
            // )); 

            $initialDeliveryCharge = $this->getInitialDeliveryCharge($client->id,$city->shipping_zone,1);
            //return  $initialDeliveryCharge;
            $package = Package::create(array_merge(
                $validator->validated(),
                ['status' => 1,
                'client' => $client->id,
                'delivery_charge'=> $initialDeliveryCharge],
                ['weight' => 1],
                ['cod' => $request->package_type == 3 ? $initialDeliveryCharge :  ($request->package_type == 2 ? 0 : $request->cod)]
            ));

            $valid = $validator->valid();

            $this->addPackagePhone($valid['phone'],$package->id);
            $this->updatePackageHistory($package->id,1,'user_created','client',$client->id,'Delivery Request Created');

            DB::commit();

            if($package){
                $response     = array(
                    'id'      => $package->id,
                ); 
                return response(['success' => true,'data'=> $response,'message' => 'Package Created Successfully!'], 200);
            }
            else{
                return response(['success' => false,'data'=> null,'message' => 'Package Created Not Successfully!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            // $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                             'data'    => null,
                             'message' => 'Oops! Something went wrong please try again later'.$e->getMessage()], 200);
        }
    }


    /*
    |--------------------------------------------------------------------------
    |Public function / Get Packge 
    |--------------------------------------------------------------------------
    */
    public function getPackge(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }


            $validation_array = [
                'waybill'          => 'required',
                'api_key'          => 'required',
            ];
            $customMessages = [
                'api_key.required'   => 'unauthorized access, invalid api key'
            ];

            $validator = Validator::make($request->all(), $validation_array,$customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            $package = Package::with('Status','City','Client','packageType')->where('client',$client->id)->where('waybill',$request->waybill)->first();

            if($package){

                $data = array(
                    'waybill'            => $package->waybill,
                    'status'             => $package->Status->title,
                    'city'               => $package->City->city,
                    'client'             => $package->Client->name,
                    'client_ref'         => $package->client_ref,
                    'recipient'          => $package->recipient,
                    'address'            => $package->address,
                    'cod'                => $package->cod,
                    'delivery_charge'    => $package->delivery_charge,
                    'cash_handling_fee'  => $package->cash_handling_fee,
                    'amount_paid'        => $package->amount_paid,
                    'client_remarks'     => $package->client_remarks,
                    'courier_remarks'    => $package->courier_remarks,
                    'weight'             => $package->weight,
                    'attempts'           => $package->attempts,
                    'item_description'   => $package->item_description,
                    'received_date'      => $package->received_date,
                    'created_at'         => $package->created_at,
                    'package_type'       => $package->packageType->package_type,
                    'last_attempted_date'=> $package->last_attempted_date,
                ); 
                $data['package_history'] = PackageHistory::select('date','remark')->where('package',$package->id)->get();

                return response(['success' => true,'data'=> $data,'message' => 'Package Found Successfully!'], 200);
            }
            else{
                return response(['success' => false,'data'=> null,'message' => 'Package Not Found!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                             'data'    => null,
                             'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }

    /*
    |--------------------------------------------------------------------------
    |Public function / Get Package for Public Tracking
    |--------------------------------------------------------------------------
    */
    public function getPackagePublic(Request $request){

        try {
            $validation_array = [
                'waybill'          => 'required',
                'phone'            => 'required|digits:10|starts_with:0',
            ];

            $customMessages = [
                'phone.digits'      => 'Phone number must be exactly 10 digits',
                'phone.starts_with' => 'Phone number must start with 0',
                'waybill.required'  => 'Waybill number is required',
                'phone.required'    => 'Phone number is required'
            ];

            $validator = Validator::make($request->all(), $validation_array, $customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            // Find package by waybill first
            $package = Package::with('Status','City','Client','packageType')->where('waybill',$request->waybill)->first();

            if(!$package){
                return response(['success' => false,'data'=> null,'message' => 'Package Not Found!'], 200);
            }

            // Check if the provided phone number matches any of the package's phone numbers
            $phoneExists = PackagePhone::where('package', $package->id)
                                    ->where('phone', $request->phone)
                                    ->exists();

            if(!$phoneExists){
                return response(['success' => false,'data'=> null,'message' => 'Invalid phone number for this package!'], 200);
            }

            // Return package details (same as the original getPackge method but without client-specific info)
            $data = array(
                'waybill'            => $package->waybill,
                'status'             => $package->Status->title,
                'city'               => $package->City->city,
                'client_ref'         => $package->client_ref,
                'recipient'          => $package->recipient,
                'address'            => $package->address,
                'cod'                => $package->cod,
                'delivery_charge'    => $package->delivery_charge,
                'cash_handling_fee'  => $package->cash_handling_fee,
                'client_remarks'     => $package->client_remarks,
                'courier_remarks'    => $package->courier_remarks,
                'weight'             => $package->weight,
                'attempts'           => $package->attempts,
                'item_description'   => $package->item_description,
                'received_date'      => $package->received_date,
                'created_at'         => $package->created_at,
                'package_type'       => $package->packageType->package_type,
                'last_attempted_date'=> $package->last_attempted_date,
            ); 
            
            // Get package history
            $data['package_history'] = PackageHistory::select('date','remark')->where('package',$package->id)->get();

            return response(['success' => true,'data'=> $data,'message' => 'Package Found Successfully!'], 200);
        }
        catch (\Throwable $e){
            // For public endpoint, we don't have client info for error email, so we'll log differently
            // You might want to log this error to your application logs instead
            \Log::error('Public Package Tracking Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'function' => __FUNCTION__,
                'waybill' => $request->waybill ?? 'N/A',
                'phone' => $request->phone ?? 'N/A'
            ]);
            
            return response(['success' => false,
                            'data'    => null,
                            'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }



    /*
    |--------------------------------------------------------------------------
    |Public function / Get Packge 
    |--------------------------------------------------------------------------
    */
    public function returnRecieved(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }

            $validation_array = [
                'waybill'          => 'required',
                'api_key'          => 'required',
            ];

            $customMessages = [
                'api_key.required'   => 'unauthorized access, invalid api key'
            ];


            $validator = Validator::make($request->all(), $validation_array,$customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            $package = Package::where('client',$client->id)->where('waybill',$request->waybill)->first();

            if($package){

                if($package->status == 12 ){
                    DB::beginTransaction();

                    $package->status = 15;
                    $package->save();
                    $this->updatePackageHistory($package->id,15,'Client received the returned package','client',$client->id,'client_recieved');

                    DB::commit();

                    return response(['success' => true,'data'=> $package->waybill,'message' => 'Package Returned Successfully!'], 200);

                }else{
                    return response(['success' => true,'data'=> null,'message' => 'This package is not ready to be returned!'], 200);
                }
            }
            else{
                return response(['success' => false,'data'=> null,'message' => 'Package Not Found!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                             'data'    => null,
                             'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }


    /*
    |--------------------------------------------------------------------------
    |Public function / Get Package List 
    |--------------------------------------------------------------------------
    */
    public function getPackageList(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }

            $validation_array = [
                'api_key'          => 'required',
                'from_date'        => 'nullable|date',
                'to_date'          => 'nullable|date|after_or_equal:from_date',
                'status'           => 'nullable|array',
                'status.*'         => 'nullable|integer|exists:status,id',
                'page'             => 'nullable|integer|min:1',
                'per_page'         => 'nullable|integer|min:1|max:100'
            ];

            $customMessages = [
                'api_key.required'       => 'unauthorized access, invalid api key',
                'to_date.after_or_equal' => 'To date must be after or equal to from date',
                'status.*.exists'        => 'Invalid status provided'
            ];

            $validator = Validator::make($request->all(), $validation_array, $customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            $query = Package::with('Status','City','Client','packageType')
                           ->where('client', $client->id);

            // Apply date filters
            if($request->filled('from_date')){
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if($request->filled('to_date')){
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Apply status filters
            if($request->filled('status')){
                $query->whereIn('status', $request->status);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $packages = $query->orderBy('created_at', 'desc')->paginate($perPage);

            if($packages->count() > 0){

                $data = [];
                foreach($packages as $package){
                    $data[] = array(
                        'id'                   => $package->id,
                        'waybill'              => $package->waybill,
                        'status'               => $package->Status->title,
                        'status_id'            => $package->status,
                        'city'                 => $package->City->city,
                        'client_ref'           => $package->client_ref,
                        'recipient'            => $package->recipient,
                        'address'              => $package->address,
                        'cod'                  => $package->cod,
                        'delivery_charge'      => $package->delivery_charge,
                        'cash_handling_fee'    => $package->cash_handling_fee,
                        'amount_paid'          => $package->amount_paid,
                        'client_remarks'       => $package->client_remarks,
                        'courier_remarks'      => $package->courier_remarks,
                        'weight'               => $package->weight,
                        'attempts'             => $package->attempts,
                        'item_description'     => $package->item_description,
                        'received_date'        => $package->received_date,
                        'created_at'           => $package->created_at,
                        'package_type'         => $package->packageType->package_type,
                        'last_attempted_date'  => $package->last_attempted_date,
                    );
                }

                $response = array(
                    'packages'        => $data,
                    'pagination'      => array(
                        'current_page'   => $packages->currentPage(),
                        'total_pages'    => $packages->lastPage(),
                        'per_page'       => $packages->perPage(),
                        'total_items'    => $packages->total(),
                        'from'           => $packages->firstItem(),
                        'to'             => $packages->lastItem()
                    )
                );

                return response(['success' => true,'data'=> $response,'message' => 'Package list retrieved successfully!'], 200);
            }
            else{
                $response = array(
                    'packages'        => [],
                    'pagination'      => array(
                        'current_page'   => 1,
                        'total_pages'    => 0,
                        'per_page'       => $perPage,
                        'total_items'    => 0,
                        'from'           => null,
                        'to'             => null
                    )
                );
                return response(['success' => true,'data'=> $response,'message' => 'No packages found!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                             'data'    => null,
                             'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }

    
    /*
    |--------------------------------------------------------------------------
    | Public function / Get Client Settlement Batches
    |--------------------------------------------------------------------------
    */
    public function getClientSettlementBatches(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }

            $validation_array = [
                'api_key'          => 'required',
                'from_date'        => 'nullable|date',
                'to_date'          => 'nullable|date|after_or_equal:from_date',
                'page'             => 'nullable|integer|min:1',
                'per_page'         => 'nullable|integer|min:1|max:100'
            ];

            $customMessages = [
                'api_key.required'       => 'unauthorized access, invalid api key',
                'to_date.after_or_equal' => 'To date must be after or equal to from date'
            ];

            $validator = Validator::make($request->all(), $validation_array, $customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            $query = ClientSettlementBatch::with('Client', 'User')
                        ->where('client', $client->id);

            // Apply date filters
            if($request->filled('from_date')){
                $query->whereDate('created_date', '>=', $request->from_date);
            }

            if($request->filled('to_date')){
                $query->whereDate('created_date', '<=', $request->to_date);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $batches = $query->orderBy('created_date', 'desc')->paginate($perPage);

            if($batches->count() > 0){

                $data = [];
                foreach($batches as $batch){
                    $data[] = array(
                        'id'                   => $batch->id,
                        'search_code'          => $batch->search_code,
                        'created_date'         => $batch->created_date,
                        'item_count'           => $batch->item_count,
                        'cod'                  => $batch->cod,
                        'delivery_charge'      => $batch->delivery_charge,
                        'cash_handling_fee'    => $batch->cash_handling_free,
                        'amount_payable'       => $batch->amount_payble,
                        'created_by'           => $batch->User ? $batch->User->name : null,
                    );
                }

                $response = array(
                    'settlement_batches' => $data,
                    'pagination'         => array(
                        'current_page'   => $batches->currentPage(),
                        'total_pages'    => $batches->lastPage(),
                        'per_page'       => $batches->perPage(),
                        'total_items'    => $batches->total(),
                        'from'           => $batches->firstItem(),
                        'to'             => $batches->lastItem()
                    )
                );

                return response(['success' => true,'data'=> $response,'message' => 'Client invoices retrieved successfully!'], 200);
            }
            else{
                $response = array(
                    'settlement_batches' => [],
                    'pagination'         => array(
                        'current_page'   => 1,
                        'total_pages'    => 0,
                        'per_page'       => $perPage,
                        'total_items'    => 0,
                        'from'           => null,
                        'to'             => null
                    )
                );
                return response(['success' => true,'data'=> $response,'message' => 'No invoices batches found!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                            'data'    => null,
                            'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Public function / Get Client Settlement Batch Items
    |--------------------------------------------------------------------------
    */
    public function getClientSettlementBatchItems(Request $request){

        try {

            $client = Client::where('api_key',$request->api_key)->first();

            if($client){
                $request->merge(['api_key' => $client->api_key]);
            }else{
                $request->merge(['api_key' => NULL]);
            }

            $validation_array = [
                'api_key'          => 'required',
                'search_code'      => 'required'
            ];

            $customMessages = [
                'api_key.required'      => 'unauthorized access, invalid api key',
                'search_code.required'  => 'search code is required'
            ];

            $validator = Validator::make($request->all(), $validation_array, $customMessages);
            
            if($validator->fails()){
                return response(['success' => false,'data'=> null,'message' => implode(" / ",$validator->messages()->all())], 200);  
            }

            // Find the settlement batch
            $batch = ClientSettlementBatch::where('search_code', $request->search_code)
                                        ->where('client', $client->id)
                                        ->first();

            if(!$batch){
                return response(['success' => false,'data'=> null,'message' => 'Client invoice not found!'], 200);
            }

            // Get settlement items with package details
            $items = ClientSettlementItem::with(['Package' => function($query) {
                        $query->select('id', 'waybill', 'client_ref');
                    }])
                    ->where('client_settlement_batch', $batch->id)
                    ->get();

            if($items->count() > 0){

                $data = [];
                foreach($items as $item){
                    $data[] = array(
                        'waybill'              => $item->Package ? $item->Package->waybill : null,
                        'client_ref'           => $item->Package ? $item->Package->client_ref : null,
                        'cod'                  => $item->cod,
                        'delivery_charge'      => $item->delivery_charge,
                        'cash_handling_fee'    => $item->cash_handling_fee,
                        'amount_payable'       => $item->amount_payable
                    );
                }

                $response = array(
                    'batch_info' => array(
                        'search_code'       => $batch->search_code,
                        'created_date'      => $batch->created_date,
                        'item_count'        => $batch->item_count,
                        'total_cod'         => $batch->cod,
                        'total_delivery_charge' => $batch->delivery_charge,
                        'total_cash_handling_fee' => $batch->cash_handling_free,
                        'total_amount_payable' => $batch->amount_payble
                    ),
                    'items' => $data
                );

                return response(['success' => true,'data'=> $response,'message' => 'Client invoice items retrieved successfully!'], 200);
            }
            else{
                return response(['success' => false,'data'=> null,'message' => 'No items found in this client invoice!'], 200);
            }
        }
        catch (\Throwable $e){
            DB::rollback();
            $this->sendErrorEmail($line_number=$e->getLine(),$function=__FUNCTION__,$error_message=$e->getMessage(),$client->id,$client->name);
            return response(['success' => false,
                            'data'    => null,
                            'message' => 'Oops! Something went wrong please try again later'], 200);
        }
    }




/*...............................................................................................................................................
    //.PPPPPPPPP..........riii......................ttt................. FFFFFFFFF..................................ttt..tiii........................
    //.PPPPPPPPPP.........riii.....................attt................. FFFFFFFFF.................................cttt..tiii........................
    //.PPPPPPPPPPP.................................attt................. FFFFFFFFF.................................cttt..............................
    //.PPPP...PPPPPPrrrrrrriiiiivv..vvvv..aaaaaa.aaattttt.eeeeee........ FFF......FFuu..uuuuu.unnnnnnn....cccccc.cccttttttiii...oooooo...onnnnnnn....
    //.PPPP...PPPPPPrrrrrrriiiiivv..vvvv.vaaaaaaaaaatttttteeeeeee....... FFF......FFuu..uuuuu.unnnnnnnn..nccccccccccttttttiii.ioooooooo..onnnnnnnn...
    //.PPPPPPPPPPPPPrrr...riiiiivv.vvvv.vvaa.aaaaa.attt.ttee.eeee....... FFFFFFFF.FFuu..uuuuu.unnn.nnnnnnnccc.cccc.cttt..tiii.iooo.ooooo.onnn.nnnnn..
    //.PPPPPPPPPP.PPrr....riii.ivvvvvvv.....aaaaaa.attt.ttee..eeee...... FFFFFFFF.FFuu..uuuuu.unnn..nnnnnncc..ccc..cttt..tiiiiioo...oooo.onnn..nnnn..
    //.PPPPPPPPP..PPrr....riii.ivvvvvvv..vaaaaaaaa.attt.tteeeeeeee...... FFFFFFFF.FFuu..uuuuu.unnn..nnnnnncc.......cttt..tiiiiioo...oooo.onnn..nnnn..
    //.PPPP.......PPrr....riii.ivvvvvv..vvaaaaaaaa.attt.tteeeeeeee...... FFF......FFuu..uuuuu.unnn..nnnnnncc.......cttt..tiiiiioo...oooo.onnn..nnnn..
    //.PPPP.......PPrr....riii..vvvvvv..vvaa.aaaaa.attt.ttee............ FFF......FFuu..uuuuu.unnn..nnnnnncc..ccc..cttt..tiiiiioo...oooo.onnn..nnnn..
    //.PPPP.......PPrr....riii..vvvvvv..vvaa.aaaaa.attt.ttee..eeee...... FFF......FFuuu.uuuuu.unnn..nnnnnnccc.cccc.cttt..tiii.iooo.ooooo.onnn..nnnn..
    //.PPPP.......PPrr....riii..vvvvv...vvaaaaaaaa.atttttteeeeeee....... FFF.......Fuuuuuuuuu.unnn..nnnn.ncccccccc.cttttttiii.ioooooooo..onnn..nnnn..
    //.PPPP.......PPrr....riii...vvvv....vaaaaaaaa.attttt.eeeeee........ FFF........uuuuuuuuu.unnn..nnnn..cccccc...cttttttiii...oooooo...onnn..nnnn..
    //...............................................................................................................................................  
*/



    /*
    |--------------------------------------------------------------------------
    |Private function / Add Packge phone
    |--------------------------------------------------------------------------
    */
    private function addPackagePhone($phones,$package){

        $phone_data=array();

        foreach($phones as $key=> $phone){   
            if($phone!=null){
                $phone_data[$key] = array('package'=>$package,'phone'=>$phone);
            }        
        }

        return PackagePhone::insert($phone_data);
    }


    /*
    |--------------------------------------------------------------------------
    | Insert to package_history table table
    |--------------------------------------------------------------------------
    */
    private function updatePackageHistory($package,$status,$description,$user_type,$user_id,$remark){

        $record = array(
            'package'       => $package,
            'remark'        => $remark,
            'description'   => $description,
            $user_type      => $user_id,
            'status'        => $status
        );

        return PackageHistory::insert($record);
    }



    /*
    |--------------------------------------------------------------------------
    | Private Function / Send Error Email 
    |--------------------------------------------------------------------------
    */
    private function sendErrorEmail($line_number,$function,$error_message,$client_id,$client_name){

        // $name   = $client_name;
        // $id     = $client_id;
       
        // Mail::send('system_error_email',
        // array(
        //     'client'            => $name,
        //     'client_id'         => $id,
        //     'function'          => $function,
        //     'line_number'       => $line_number,
        //     'messages'          => $error_message,
        // ), function($message) use ($function)
        //   {
        //      $message->from('info@ceylonex.lk');
        //      $message->to('help@xypherlabs.com');
        //      $message->subject('ceylonex.lk - API - System Error - '.$function);
        //   });

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Private Function / get client
    |--------------------------------------------------------------------------
    */
    private function getClient($client_id){

        $client = Client::find($client_id);
        return $client;

    }

     /*
    |--------------------------------------------------------------------------
    | Private Function / Get initial delivery charge
    |--------------------------------------------------------------------------
    */
    private function getInitialDeliveryCharge($client,$shipping_zone,$weight){
        //return $shipping_zone.'='.$client;

        $data = ClientPricing::where('client',$client)->where('shipping_zone',$shipping_zone)->first();

        $kg = ceil($weight/ 1);

        $delivery_charge_for_additional_kg = $data->delivery_charge_for_additional_kg * ($kg-1);
        $delivery_charge = $data->delivery_charge +  $delivery_charge_for_additional_kg;
        return  $delivery_charge;
    }





}
