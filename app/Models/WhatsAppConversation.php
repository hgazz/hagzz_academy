<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = ['academy_id', 'contact_type', 'contact_id', 'contact_name', 'phone', 'unread_count', 'last_message_at', 'last_inbound_at', 'service_window_expires_at'];
    protected $casts = ['last_message_at' => 'datetime', 'last_inbound_at' => 'datetime', 'service_window_expires_at' => 'datetime'];

    public function academy() { return $this->belongsTo(Academies::class, 'academy_id'); }
    public function messages() { return $this->hasMany(WhatsAppMessage::class, 'conversation_id'); }
    public function serviceWindowIsOpen(): bool { return $this->service_window_expires_at?->isFuture() === true; }
}
