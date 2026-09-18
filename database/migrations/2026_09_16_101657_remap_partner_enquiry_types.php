<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Partner enquiries store the organisation-type label in the message body.
        // The type field grew from 4 to 6 options; map the old labels to the new ones
        // so existing enquiries stay consistent with the current form (and valid).
        DB::table('contact_forms')
            ->where('message', 'like', '%Aanvraag partnerschap%')
            ->where('message', 'like', '%(vzw / vereniging)%')
            ->update(['message' => DB::raw("REPLACE(message, '(vzw / vereniging)', '(ASBL)')")]);

        DB::table('contact_forms')
            ->where('message', 'like', '%Aanvraag partnerschap%')
            ->where('message', 'like', '%(Gemeente / overheid)%')
            ->update(['message' => DB::raw("REPLACE(message, '(Gemeente / overheid)', '(Gemeente)')")]);
    }

    public function down(): void
    {
        DB::table('contact_forms')
            ->where('message', 'like', '%Aanvraag partnerschap%')
            ->where('message', 'like', '%(ASBL)%')
            ->update(['message' => DB::raw("REPLACE(message, '(ASBL)', '(vzw / vereniging)')")]);

        DB::table('contact_forms')
            ->where('message', 'like', '%Aanvraag partnerschap%')
            ->where('message', 'like', '%(Gemeente)%')
            ->update(['message' => DB::raw("REPLACE(message, '(Gemeente)', '(Gemeente / overheid)')")]);
    }
};
