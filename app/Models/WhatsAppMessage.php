<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = ['academy_id', 'conversation_id', 'campaign_id', 'provider_message_id', 'direction', 'message_type', 'body', 'template_name', 'status', 'error_message', 'payload', 'sent_at', 'delivered_at', 'read_at'];
    protected $casts = ['payload' => 'array', 'sent_at' => 'datetime', 'delivered_at' => 'datetime', 'read_at' => 'datetime'];

    public function conversation() { return $this->belongsTo(WhatsAppConversation::class, 'conversation_id'); }
    public function campaign() { return $this->belongsTo(WhatsAppCampaign::class, 'campaign_id'); }
}
