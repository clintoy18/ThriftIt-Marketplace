import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    },
    // Enable automatic reconnection
    enabledTransports: ['ws', 'wss'],
});

// Debug: Log Echo initialization
console.log('Echo initialized:', !!window.Echo);
console.log('Pusher key:', import.meta.env.VITE_PUSHER_APP_KEY ? 'Set' : 'Missing');
console.log('CSRF token:', csrfToken ? 'Set' : 'Missing');

// Add error handler for authentication failures (only log once per error type)
let errorLogCount = {};
window.Echo.connector.pusher.connection.bind('error', function(err) {
    const errorCode = err?.error?.data?.code || err?.error?.code || 'unknown';
    const errorKey = `${errorCode}_${err?.error?.message || 'unknown'}`;
    
    // Only log the same error once every 5 seconds to avoid spam
    if (!errorLogCount[errorKey] || Date.now() - errorLogCount[errorKey] > 5000) {
        errorLogCount[errorKey] = Date.now();
        
        if (errorCode === 403) {
            console.error('❌ Broadcasting authentication failed (403). User may not be authenticated.');
            console.error('Error details:', err);
        } else if (errorCode === 5001 || errorCode === 1006) {
            // Connection closed errors - don't spam console
            console.warn('⚠️ Pusher connection issue (will retry):', errorCode);
        } else {
            console.error('Pusher connection error:', err);
        }
    }
});

// Add connection state monitoring
let lastState = null;
window.Echo.connector.pusher.connection.bind('state_change', function(states) {
    // Only log state changes, not every state check
    if (states.current !== lastState) {
        console.log('Pusher connection state changed:', states.previous, '→', states.current);
        lastState = states.current;
        
        if (states.current === 'failed') {
            console.error('❌ Pusher connection failed. Check authentication and network.');
        } else if (states.current === 'connected') {
            console.log('✅ Pusher connection established');
        }
    }
});

// Public channel (optional if you’re only using private)
window.Echo.channel('chat-channel')
    .listen('.message.sent', (e) => {
        console.log('Received message:', e.message);

        const newMessageHTML = `
            <div class="mb-4">
                <strong>${e.message.user.fname} ${e.message.user.lname}:</strong>
                <span>${e.message.message}</span><br>
                <small class="text-gray-500">just now</small>
            </div>
            <hr>
        `;

        const container = document.getElementById('messages-container');
        if (container) {
            container.insertAdjacentHTML('beforeend', newMessageHTML);
            container.scrollTop = container.scrollHeight;
        }
    });

// Private message listener
const currentUserId = Number(document.querySelector('meta[name="user-id"]')?.content);
const recipientId = Number(document.querySelector('meta[name="recipient-id"]')?.content);

if (currentUserId && csrfToken) {
    // Subscribe to private channel with error handling
    try {
        const privateChannel = window.Echo.private(`chat.user.${currentUserId}`);
        
        // Listen for subscription success
        privateChannel.subscribed(() => {
            console.log('✅ Successfully subscribed to private channel:', `chat.user.${currentUserId}`);
        });
        
        // Listen for subscription errors
        privateChannel.error((error) => {
            console.error('❌ Error subscribing to private channel:', error);
            if (error.status === 403) {
                console.error('403 Forbidden: Check if user is authenticated and channel authorization is correct.');
            }
        });
        
        privateChannel.listen('.private-message', (e) => {
            console.log('Received real-time message:', e);
            
            // Only show message if we're in the correct chat
            if (!(e.sender.id === recipientId || e.sender.id === currentUserId)) return;

            const container = document.getElementById('private-messages-container');
            if (!container) return;

            // Create message bubble HTML matching your design
            const isOwnMessage = e.sender.id === currentUserId;
            const messageHTML = createMessageBubble(e.message, e.sender, isOwnMessage);

            // Add message to container
            container.insertAdjacentHTML('beforeend', messageHTML);
            
            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        });
    } catch (error) {
        console.error('❌ Error setting up private channel:', error);
    }
} else {
    console.warn('⚠️ Cannot subscribe to private channel: User ID or CSRF token missing');
    console.log('User ID:', currentUserId, 'CSRF Token:', csrfToken ? 'Present' : 'Missing');
}

function createMessageBubble(message, sender, isOwnMessage) {
    const timeAgo = 'just now';
    const avatarUrl = sender.profile_pic_url || sender.profile_pic || sender.profileImageUrl;
    const initials = `${(sender.fname || '?').charAt(0)}${(sender.lname || '?').charAt(0)}`.toUpperCase();
    const avatarHTML = avatarUrl
        ? `<img src="${avatarUrl}" class="w-8 h-8 rounded-full object-cover" alt="${sender.fname || ''} ${sender.lname || ''}">`
        : `<div class="w-8 h-8 bg-[#B59F84] text-white rounded-full flex items-center justify-center text-xs font-semibold">
              ${initials}
           </div>`;

    const bubbleClasses = isOwnMessage
        ? 'bg-gradient-to-r from-[#634600] to-[#B59F84] text-white rounded-2xl rounded-br-md'
        : 'bg-white text-[#634600] rounded-2xl rounded-bl-md shadow-sm border border-[#B59F84]';

    const safeMessage = message.message
        ? message.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>")
        : '';

    const imageHTML = message.image_url 
        ? `<div class="mb-2"><img src="${message.image_url}" class="max-w-xs rounded-lg shadow-sm"></div>`
        : '';

    return `
        <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-2 message-item">
            <div class="flex max-w-[85%] sm:max-w-xs lg:max-w-md ${isOwnMessage ? 'flex-row-reverse' : 'flex-row'} items-end space-x-2">
                <div class="w-8 h-8 rounded-full flex-shrink-0 ${isOwnMessage ? 'ml-1' : 'mr-1'}">${avatarHTML}</div>
                <div class="flex flex-col ${isOwnMessage ? 'items-end' : 'items-start'}">
                    <div class="px-4 py-2 text-sm break-words ${bubbleClasses}">
                        ${imageHTML}
                        ${safeMessage}
                    </div>
                    <div class="mt-1 ${isOwnMessage ? 'text-right' : 'text-left'}">
                        <span class="text-xs text-gray-500">${timeAgo}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}


// Notification listener
const authUserId = Number(document.querySelector('meta[name="user-id"]')?.content);

if (authUserId) {
    window.Echo.private(`notifications-channel.${authUserId}`)
        .listen('.comment.notification', (e) => {
            console.log("🔔 New Comment Notification:", e);

            const isReply = e.type === 'comment_reply';
            const baseMessage = isReply
                ? `${e.from_user} replied to your comment`
                : `${e.from_user} commented on your listing`;

            showNotificationToast(`${baseMessage}: "${e.content}"`);

            window.dispatchEvent(new CustomEvent('new-notification', {
                detail: {
                    id: Date.now(),
                    data: {
                        from_user: e.from_user,
                        content: e.content,
                        product_id: e.product_id,
                        donation_id: e.donation_id,
                        message: baseMessage,
                    },
                    created_at: new Date().toISOString(),
                    is_read: false,
                }
            }));
        });
}

// Product Status Notification listener
if (authUserId) {
    window.Echo.private(`notifications-channel.${authUserId}`)
        .listen('.product.status.notification', (e) => {
            console.log("🔔 Product Status Notification:", e);

            // Show friendly toast based on status
            showNotificationToast(e.message); // e.message already contains "approved" or "rejected" text

            // Dispatch event for Alpine or any frontend updates
            window.dispatchEvent(new CustomEvent('new-notification', {
                detail: {
                    id: Date.now(),
                    data: {
                        product_id: e.id,
                        product_name: e.name,
                        status: e.status,
                        message: e.message,
                        from_user: e.from_user,
                    },
                    created_at: new Date().toISOString(),
                    is_read: false,
                }
            }));
        });
}

// Order Placed Notification listener
if (authUserId) {
    window.Echo.private(`notifications-channel.${authUserId}`)
        .listen('.order.placed.notification', (e) => {
            console.log("🛒 New Order Notification:", e);

            // Show a nice toast message
            showNotificationToast(`🛍️ New Order: ${e.buyer_name} placed an order for ${e.product_name}`);

            // Dispatch event for Alpine/Live updates
            window.dispatchEvent(new CustomEvent('new-notification', {
                detail: {
                    id: Date.now(),
                    data: {
                        order_id: e.id,
                        product_name: e.product_name,
                        buyer_name: e.buyer_name,
                        message: e.message,
                    },
                    created_at: new Date().toISOString(),
                    is_read: false,
                }
            }));
        });
}


// Function to show toast notification
function showNotificationToast(message) {
    const toast = document.createElement("div");
    toast.className = "fixed bottom-4 right-4 bg-[#B59F84] text-white px-4 py-2 rounded-lg shadow-lg z-50";
    toast.innerText = message;

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// ============================================
// GLOBAL INCOMING CALL HANDLERS
// These work from any page, not just the chat page
// ============================================
if (authUserId && window.Echo) {
    console.log('Setting up global incoming call listeners for user:', authUserId);
    
    // Debug: Check Echo connection
    try {
        const testChannel = window.Echo.private(`chat.user.${authUserId}`);
        console.log('Echo channel created:', !!testChannel);
        console.log('Echo connection status:', window.Echo.connector?.socket?.readyState || 'unknown');
    } catch (error) {
        console.error('Error creating Echo channel:', error);
    }

    // Listen for call invitations via Laravel Event (more reliable than whispers)
    const globalCallChannel = window.Echo.private(`chat.user.${authUserId}`);
    
    // Add subscription success/error handlers
    globalCallChannel.subscribed(() => {
        console.log('✅ Successfully subscribed to global call channel');
    });
    
    globalCallChannel.error((error) => {
        console.error('❌ Error subscribing to global call channel:', error);
        if (error.status === 403) {
            console.error('403 Forbidden: Authentication failed. Please refresh the page and log in again.');
        }
    });
    
    globalCallChannel.listen('.call-invitation', (data) => {
            console.log('Received call invitation event:', data);
            
            if (data.action === 'invite' && data.caller_id) {
                const callerInfo = data.caller || { name: 'Someone' };
                const callerName = callerInfo.name || `${callerInfo.fname || ''} ${callerInfo.lname || ''}`.trim() || 'Someone';
                
                if (data.call_type === 'video') {
                    handleIncomingVideoCallGlobal(data.caller_id, callerName, callerInfo);
                } else if (data.call_type === 'audio') {
                    handleIncomingAudioCallGlobal(data.caller_id, callerName, callerInfo);
                }
            } else if (data.action === 'end' || data.action === 'rejected') {
                // Handle call end/rejection
                const audioModal = document.getElementById('incoming-audio-call-modal');
                const videoModal = document.getElementById('incoming-video-call-modal');
                
                if (audioModal) {
                    if (typeof endIncomingAudioCall === 'function') {
                        endIncomingAudioCall();
                    } else {
                        audioModal.remove();
                        document.body.style.overflow = '';
                    }
                }
                
                if (videoModal) {
                    if (typeof endIncomingVideoCall === 'function') {
                        endIncomingVideoCall();
                    } else {
                        videoModal.remove();
                        document.body.style.overflow = '';
                    }
                }
            } else if (data.action === 'accepted') {
                // Call was accepted by recipient
                console.log('Call accepted by recipient');
                // The chat page handler will take care of this
            }
        });

    // Also listen for whispers as fallback (for backward compatibility)
    // Note: These use the same channel, so they'll inherit the subscription handlers above
    globalCallChannel.listenForWhisper('audio-call-invitation', (data) => {
            console.log('Received audio call invitation (whisper):', data);
            if (data.action === 'invite' && data.from) {
                const callerName = data.fromName || 'Someone';
                handleIncomingAudioCallGlobal(data.from, callerName);
            }
        });

    globalCallChannel.listenForWhisper('video-call-invitation', (data) => {
            console.log('Received video call invitation (whisper):', data);
            if (data.action === 'invite' && data.from) {
                const callerName = data.fromName || 'Someone';
                handleIncomingVideoCallGlobal(data.from, callerName);
            }
        });

    // Listen for incoming audio call offers
    globalCallChannel.listenForWhisper('audio-call-offer', (data) => {
            console.log('Received audio call offer:', data);
            if (data.from && data.offer) {
                // Only handle if we're on the chat page and have the handler
                if (typeof handleAudioCallOffer === 'function') {
                    handleAudioCallOffer(data.from, data.offer);
                }
            }
        });

    // Listen for incoming video call offers
    globalCallChannel.listenForWhisper('video-call-offer', (data) => {
            console.log('Received video call offer:', data);
            if (data.from && data.offer) {
                // Only handle if we're on the chat page and have the handler
                if (typeof handleVideoCallOffer === 'function') {
                    handleVideoCallOffer(data.from, data.offer);
                }
            }
        });

    // Listen for ICE candidates (both audio and video)
    globalCallChannel.listenForWhisper('ice-candidate-audio', (data) => {
        if (data.from && data.candidate && window.audioPeerConnection) {
            window.audioPeerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                .catch(error => console.error('Error adding ICE candidate:', error));
        }
    });

    globalCallChannel.listenForWhisper('ice-candidate', (data) => {
        if (data.from && data.candidate && window.peerConnection) {
            window.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                .catch(error => console.error('Error adding ICE candidate:', error));
        }
    });
} else {
    console.warn('⚠️ Cannot set up global call listeners: User ID missing or Echo not available');
    console.log('User ID:', authUserId, 'Echo available:', !!window.Echo);
}

// Global handler for incoming audio call (works from any page)
function handleIncomingAudioCallGlobal(callerId, callerName = 'Someone', callerInfo = null) {
    // Prevent duplicate modals
    if (document.getElementById('incoming-audio-call-modal')) {
        console.log('Audio call modal already exists, ignoring duplicate');
        return;
    }
    
    console.log('Handling incoming audio call from:', callerId, callerName);
    
    // Check if we're on the chat page with this user - if so, let the chat page handler take over
    const currentPath = window.location.pathname;
    const chatPath = `/private-chat/${callerId}`;
    if (currentPath === chatPath && typeof handleIncomingAudioCall === 'function') {
        console.log('On chat page, delegating to chat page handler');
        // We're on the chat page, let the chat page handler deal with it
        handleIncomingAudioCall(callerId, callerName);
        return;
    }
    
    showIncomingCallModal('audio', callerId, callerName, callerInfo);
}

// Global handler for incoming video call (works from any page)
function handleIncomingVideoCallGlobal(callerId, callerName = 'Someone', callerInfo = null) {
    // Prevent duplicate modals
    if (document.getElementById('incoming-video-call-modal')) {
        console.log('Video call modal already exists, ignoring duplicate');
        return;
    }
    
    console.log('Handling incoming video call from:', callerId, callerName);
    
    // Check if we're on the chat page with this user - if so, let the chat page handler take over
    const currentPath = window.location.pathname;
    const chatPath = `/private-chat/${callerId}`;
    if (currentPath === chatPath && typeof handleIncomingVideoCall === 'function') {
        console.log('On chat page, delegating to chat page handler');
        // We're on the chat page, let the chat page handler deal with it
        handleIncomingVideoCall(callerId, callerName);
        return;
    }
    
    showIncomingCallModal('video', callerId, callerName, callerInfo);
}

// Show incoming call modal (works from any page)
function showIncomingCallModal(type, callerId, callerName, callerInfo = null) {
    const modal = document.createElement('div');
    modal.id = `incoming-${type}-call-modal`;
    modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
    
    const callTypeLabel = type === 'audio' ? 'Audio Call' : 'Video Call';
    const acceptIcon = type === 'audio' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>';
    
    // Use avatar if available
    const avatarHTML = callerInfo && callerInfo.avatar
        ? `<img src="${callerInfo.avatar}" alt="${callerName}" class="w-full h-full rounded-full object-cover">`
        : `<span class="text-white text-2xl font-semibold">${callerName.charAt(0).toUpperCase()}</span>`;
    
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center max-w-md w-full mx-4">
            <div class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse overflow-hidden">
                ${avatarHTML}
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Incoming ${callTypeLabel}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8">from ${callerName}</p>
            <div class="flex items-center justify-center gap-4">
                <button onclick="rejectIncomingCall('${type}', ${callerId})" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors" title="Decline">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <button onclick="acceptIncomingCall('${type}', ${callerId})" class="p-4 bg-green-600 hover:bg-green-700 rounded-full text-white transition-colors" title="Accept">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${acceptIcon}
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Auto-reject after 30 seconds
    window[`incoming${type.charAt(0).toUpperCase() + type.slice(1)}CallTimeout`] = setTimeout(() => {
        rejectIncomingCall(type, callerId);
    }, 30000);
}

// Accept incoming call (redirects to chat page if not already there)
function acceptIncomingCall(type, callerId) {
    clearTimeout(window[`incoming${type.charAt(0).toUpperCase() + type.slice(1)}CallTimeout`]);
    const modal = document.getElementById(`incoming-${type}-call-modal`);
    if (modal) modal.remove();
    document.body.style.overflow = '';
    
    console.log('Accepting call:', type, 'from:', callerId);
    
    // Send acceptance notification to caller
    sendCallResponse(callerId, type, 'accepted')
        .then(() => {
            console.log('Call acceptance sent');
        })
        .catch(error => {
            console.error('Error sending call acceptance:', error);
        });
    
    // Check if we're on the chat page with this user
    const currentPath = window.location.pathname;
    const chatPath = `/private-chat/${callerId}`;
    
    if (currentPath === chatPath) {
        // We're already on the chat page, use the existing handler
        if (type === 'audio' && typeof acceptIncomingAudioCall === 'function') {
            acceptIncomingAudioCall(callerId);
        } else if (type === 'video' && typeof acceptIncomingVideoCall === 'function') {
            acceptIncomingVideoCall(callerId);
        }
    } else {
        // Redirect to chat page - the call will be handled there
        window.location.href = chatPath + '?accept_call=' + type;
    }
}

// Send call response (accept/reject) to the caller
function sendCallResponse(callerId, callType, response) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    return fetch('/api/call/response', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            caller_id: callerId,
            call_type: callType,
            response: response
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to send call response');
        }
        return response.json();
    });
}

// Reject incoming call
function rejectIncomingCall(type, callerId) {
    clearTimeout(window[`incoming${type.charAt(0).toUpperCase() + type.slice(1)}CallTimeout`]);
    const modal = document.getElementById(`incoming-${type}-call-modal`);
    if (modal) modal.remove();
    document.body.style.overflow = '';
    
    console.log('Rejecting call:', type, 'from:', callerId);
    
    // Send rejection notification to caller via API
    sendCallResponse(callerId, type, 'rejected')
        .then(() => {
            console.log('Call rejection sent');
        })
        .catch(error => {
            console.error('Error sending call rejection:', error);
        });
    
    // Also call the specific reject function if it exists
    if (type === 'audio' && typeof rejectIncomingAudioCall === 'function') {
        rejectIncomingAudioCall();
    } else if (type === 'video' && typeof rejectIncomingVideoCall === 'function') {
        rejectIncomingVideoCall();
    }
}


