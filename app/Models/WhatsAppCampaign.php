<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppCampaign extends Model
{
    protected $fillable = ['academy_id', 'name', 'audience', 'purpose', 'message_type', 'template_name', 'template_language', 'template_parameters', 'body', 'status', 'total_recipients', 'sent_count', 'failed_count', 'started_at', 'completed_at'];
    protected $casts = ['template_parameters' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    public function messages() { return $this->hasMany(WhatsAppMessage::class, 'campaign_id'); }
}
