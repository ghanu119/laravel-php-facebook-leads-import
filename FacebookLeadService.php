<?php

namespace App\Services;

use FacebookAds\Api;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookLeadService {

    /**
     * REFERENCES:
     *      - https://developers.facebook.com/docs/pages/access-tokens/#get-a-page-access-token
     *      - https://impressads.com/blogs/index/how-to-collect-facebook-leads-in-your-own-crm#:~:text=Subscribe%20for%20leadgen%20in%20the,gave%20in%20the%20above%20step
     *      - https://developers.facebook.com/docs/marketing-api/guides/lead-ads/quickstart/webhooks-integration/#install-app
     *
     *
     * Steps:
     * 1. Create app
     * 2. Create a webhook url
     * 3. Subscribe the webhook to the page > leadgen
     * 4. Get the Page ID:
     *      - Visit the page using the facebook id
     *      - Select About Tab
     *      - Select Page Transparancy under About Tab
     *      - Get Page ID
     * 5. Then visit the graph explorer screen: https://developers.facebook.com/tools/explorer/
     * 6. Select the permissions:
     *      - pages_show_list
     *      - ads_management
     *      - ads_read
     *      - leads_retrieval
     *      - pages_read_engagement
     *      - pages_manage_metadata
     *      - pages_manage_ads
     * 7. Then Visit: https://developers.facebook.com/tools/accesstoken/#_=_
     * 8. Copy User token from the required APP
     * 9. To retrieve the page access token:
     *      PAGE_ACCESS_TOKEN set as ACCESS_TOKEN using ENV;
     *      [ $this->fbApi->call( '/PAGE-ID/subscribed_apps','POST', [ 'subscribed_fields'=>'leadgen'] ); ]
     *
     *      Replace PAGE-ID with the page id and hit the URL: https://graph.facebook.com/PAGE-ID?fields=access_token&access_token=USER-ACCESS-TOKEN
     *
     * 10. Lead Testing:
     *          - https://developers.facebook.com/tools/lead-ads-testing/#_=
     *
     */

    /**
     * Access Token [ User Token ] : https://developers.facebook.com/tools/accesstoken/#_=_
     *
     */

    private $fbApi;
    private $log;

    public function __construct()
    {
        $this->log = Log::channel('stack');

        // You can directly paste the APP_ID, APP_SECRET and ACCESS_TOKEN here to configure the account
        Api::init(
            config('services.facebook_ads.app_id'),
            config('services.facebook_ads.app_secret'),
            config('services.facebook_ads.access_token')
        );
        $this->fbApi = Api::instance();
    }

    public function fetchLeadData ( $leadId = null, $formId = null ){
        if( !is_null( $leadId ) ){
            $formData = [];
            if( !empty( $formId ) ){

                // It will fetch the form related data like from which form the data receved.
                $formData = $this->fetchFormData( $formId );
            }
            try{
                $lead = $this->fbApi->call( '/'.$leadId );
                $leadData = $lead->getBOdy();
                $leadData = json_decode( $leadData, true );

                // All the lead related data will be fetched inside this.
                $fieldData = data_get( $leadData , 'field_data', []);
                if( !empty( $fieldData ) ){
                   
                    $fieldData[] = [
                        'name' => 'platform',
                        'values' => [ 'fb' ]
                    ];

                    /**
                     * Here your logic will be gone...
                     */


                }else{
                    $this->log->error( 'ERR_LEAD_NOT_FOUND', [
                        'lead_id' => $leadId
                    ] );

                }

            }catch( Throwable $th ){
                $this->log->info( $th );
            }
        }

        return null;
    }

    public function fetchFormData ( $formId = null ){
        if( !empty( $formId ) ){
            try{

                $form = $this->fbApi->call( '/'.$formId );
                $formData = $form->getBOdy();
                $formData = json_decode( $formData, true );
                return $formData;
            }catch( Throwable $th ){
                $this->log->info(  $th );
            }
        }
        return null;
    }

}
