<?php
session_start(); // Ensure the session is started

require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51PLV5u08ENRXIGZxOob6mM80X2w4B3BMwyeJop4FVWENIobht0bQ4HM0D34uvsaujY0nPLIE5l2ZRLjP1o149KDM000BICGkNd');

header('Content-Type: application/json');

# Check if user is in session
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'error' => 'User is not logged in.',
    ]);
    exit;
}
# retrieve json from POST body
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

$intent = null;
try {
    if (isset($json_obj->payment_method_id)) {
        # Create the PaymentIntent
        $intent = \Stripe\PaymentIntent::create([
            'payment_method' => $json_obj->payment_method_id,
            'amount' => $_SESSION['total_amount'],
            'currency' => 'ZAR',
            'confirmation_method' => 'manual',
            'confirm' => true,
            'return_url' => 'https://mikniks.great-site.net/MikNiks/index.php'
        ]);
    }
    if (isset($json_obj->payment_intent_id)) {
        $intent = \Stripe\PaymentIntent::retrieve(
            $json_obj->payment_intent_id
        );
        $intent->confirm([
            'return_url' => 'https://mikniks.great-site.net/MikNiks/index.php'
        ]);
    }
    generateResponse($intent);
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Stripe API error: ' . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}

function generateResponse($intent)
{
    if ($intent->status == 'requires_action' &&
        $intent->next_action->type == 'use_stripe_sdk') {
        echo json_encode([
            'requires_action' => true,
            'payment_intent_client_secret' => $intent->client_secret,
        ]);
    } else if ($intent->status == 'succeeded') {
        echo json_encode([
            "success" => true,
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid PaymentIntent status']);
    }
}
?>
