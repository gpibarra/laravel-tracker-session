<?php

return [

   /*
    * Cookies
    */
    'cookie_name_object' => 'trackerSession_Obj',
    'cookie_name_id' => 'trackerSession_Id',
    'cookie_name_boolean_request_cookie_id' => 'trackerSession_required_cookie_id',

    /*
     * This model will be used to session.
     * It should extend the gpibarra\TrackerSession\Models\TrackerSession class
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'session_model' => gpibarra\TrackerSession\Models\TrackerSession::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the TrackerSession model shipped with this package.
     */
    'table_name' => 'sessions',

    /*
     * Old Logs Clear
     * When the clean-command is executed, all authentication logs older than
     * the number of days specified here will be deleted.
     */
    'older' => 365,

];
