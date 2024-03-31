<?php
// Check if form is submitted
if(isset($_POST['submit'])) {
    // Set timezone

    // date_default_timezone_set('Africa/Nairobi');

    // Access token credentials
    $consumerKey = '3yMZoScKq08N8pZO3K5nsL3jrdiOZPBStNS2Rl6fQP0Q27Yf';
    $consumerSecret = 'egq0ADPRDSRJ4MDm5NPXli4Bjv4FixpUxMqlH7GcRtz5OnP69GzyHdzcmVpe0MsE';

    // M-PESA details
    $BusinessShortCode = '174379';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

    // Callback URL
    $CallBackURL = 'https://arcane-retreat-18416-20a639b8efcc.herokuapp.com/callback_url.php';

    // Get phone number and amount from form
    $PartyA = $_POST['phone'];
    $Amount = $_POST['amount'];

    // Generate timestamp and password
    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

    // Get access token
    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $headers = ['Content-Type:application/json; charset=utf8'];
    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $result = json_decode($result);
    $access_token = $result->access_token;  
    curl_close($curl);

    // Initiate transaction
    $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $stkheader = ['Content-Type: application/json','Authorization:Bearer '.$access_token];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $initiate_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
    $curl_post_data = array(
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $Amount,
        'PartyA' => $PartyA,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $PartyA,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => 'Borangi Wifi',
        'TransactionDesc' => 'wifi Payment'
    );
    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    
    // Display response
    echo $curl_response;
} else {
    echo 'Form not submitted.';
}
?>




<!-- new code -->
<?php
// Include the HTML content and styles from index.html
include('index.html');

// Define plans (replace with your actual plan data)
$plans = [
    [
        "name" => "Starter",
        "price" => 10,
        "duration" => 2, // Hours
        "data" => 4000,  // MB
    ],
    [
        "name" => "Standard",
        "price" => 20,
        "duration" => 4,  // Hours
        "data" => 1500,  // MB
    ],
    // ... Add more plans here ...
];

// Function to convert duration to human-readable format
function getDurationText($duration) {
    $hours = $duration;
    $days = floor($hours / 24);
    $remainingHours = $hours % 24;

    if ($hours < 24) {
        return "$hours hour" . ($hours !== 1 ? 's' : '');
    } else if ($days > 0 && $remainingHours > 0) {
        return "$days day" . ($days !== 1 ? 's' : '') . " $remainingHours hour" . ($remainingHours !== 1 ? 's' : '');
    } else if ($days > 0) {
        return "$days day" . ($days !== 1 ? 's' : '');
    } else {
        return "$remainingHours hour" . ($remainingHours !== 1 ? 's' : '');
    }
}

// Handle form submission (assuming you have a form with these elements)
if (isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['plan_selected'])) {
    $selectedPlanIndex = $_POST['plan_selected'];
    $selectedPlan = $plans[$selectedPlanIndex];

    // Simulate M-Pesa payment success (replace with your actual payment processing)
    $paymentSuccess = true;

    if ($paymentSuccess) {
        // Process user subscription based on selected plan (replace with your logic)
        echo "<script>alert('Payment of KSh {$selectedPlan['price']} for the {$selectedPlan['name']} plan successful!')</script>";
    } else {
        echo "<script>alert('Payment failed. Please try again.')</script>";
    }
}
?>

<header>
    </header>

<section id="hero">
    </section>

<section id="plans">
    <h2>SUBSCRIBE TO A PLAN</h2>
    <div class="plan-cards">
        </div>
</section>

<footer>
    </footer>

<script>
const planCardsContainer = document.querySelector('.plan-cards');
const selectButtons = document.querySelectorAll('.plan-select-button');

function renderPlans() {
    plans.forEach((plan, index) => {
        const planCard = document.createElement('div');
        planCard.classList.add('plan-card');

        const durationText = getDurationText(plan.duration);

        planCard.innerHTML = `
            <h3>${plan.name}</h3>
            <p>Price: KSh ${plan.price}</p>
            <p>Duration: ${durationText}</p>
            <p>Data: <span class="math-inline">\{plan\.data\} MB</p\>
<button class\="plan\-select\-button" data\-index\="</span>{index}">Select</button>
        `;
        planCardsContainer.appendChild(planCard);
    });

    selectButtons.forEach(button => {
        button.addEventListener('click', handlePlanSelection);
    });
}

function handlePlanSelection(event) {
    // Update form with selected plan information (replace with your form selectors)
    const selectedPlanIndex = event.target.dataset.index;
    const selectedPlan = plans[selectedPlanIndex];
    document.getElementById('plan_name').value = selectedPlan.name;
    document.getElementById('plan_price').value = selectedPlan.price;
}

renderPlans();

// Add your existing JavaScript code for video control, etc. (if applicable)
</script>

