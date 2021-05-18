<?php
/*
echo '[';

for($i = 0; $i < 200; $i++){

    echo '"'.mt_rand().'"'."\n";

    if($i + 1 < 200){
        echo ',';
    }
}

echo ']';

exit;
*/

//For live version
$dsn = 'mysql:host=premium163.web-hosting.com;dbname=unifghiu_coindesk;charset=utf8';
$usr = 'unifghiu_coindesk';
$pwd = 'vollgeschissen123';

//For local testing
//$dsn = 'mysql:host=localhost;dbname=coindesk;charset=utf8';
//$usr = 'root';
//$pwd = 'root';

$pdo = new PDO($dsn, $usr, $pwd);

$session = false;
$session_msg = '';

if(isset($_REQUEST['session']) && isset($_REQUEST['address'])){

    // we first check if the given session is valid from the json file

    $session = true;
    $session_links = json_decode(file_get_contents(__DIR__.'/linkomat.json'));

    if( !in_array($_REQUEST['session'], $session_links) ){

        $session_msg = 'Invalid session link!';

    } else {

        // if valid, we check if the wallet has been claime desk that are NOT from the sessions

        $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`address`) = ? And `signature` <> '' And processed = 1 And `Code` Not Like 'sess%'");
        $stmt->execute([strtolower($_REQUEST['address'])]);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // if he did, he is allowed to claim for the given session
        if (count($result) != 0) {

            $desk = 0;
            $maxdesk = 0;

            // now we check the parameters for the session because each session may have different $DESK and max. desks per session

            $row = 1;
            if (($handle = fopen(__DIR__."/linkomat.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    if($num == 3 && $data[0] == $_REQUEST['session']){
                        $desk = $data[1];
                        $maxdesk = $data[2];
                        break;
                    }
                }
                fclose($handle);
            }

            // if parameters have been found, continue
            if($desk > 0 && $maxdesk > 0) {

                // now we check if that wallet received desk already for the session

                $stmt = $pdo->prepare("SELECT `\$DESK Value` FROM `coindeskemails` WHERE `Recipient` = ? And `address` = ?");
                $stmt->execute(['Session' . $_REQUEST['session'],strtolower($_REQUEST['address'])]);
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // if the wallet received desk already, show an error
                if(count($result) != 0){

                    $session_msg = 'You already claimed $DESK for this session.';

                }else {

                    // if not check first if all max. desk have been claimed or not

                    $stmt = $pdo->prepare("SELECT `\$DESK Value` FROM `coindeskemails` WHERE `Recipient` = ?");
                    $stmt->execute(['Session' . $_REQUEST['session']]);
                    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $claimed_amount = count($result);

                    $claimed_for_session = 0;
                    for ($i = 0; $i < $claimed_amount; $i++) {
                        $claimed_for_session += $result[$i];
                    }

                    // if they didn't get claimed yet, create a new code on the fly, just for this wallet
                    // such that it can be picked up by the js program to actually send the desk and test-eth
                    if (intval($claimed_for_session) < intval($maxdesk)) {

                        $code = 'sess' . uniqid();

                        $stmt = $pdo->prepare("Insert Into `coindeskemails` (`Code`,`\$DESK Value`,`Recipient`,`signature`) Values (?,?,?,'')");
                        $stmt->execute([$code, $desk, 'Session' . $_REQUEST['session']]);

                        $_REQUEST['Code'] = $code;

                    } else {

                        // if not show an error

                        $session_msg = 'The amount of $DESK for this session has been depleted.';
                    }
                }

            }else{

                // if not show an error

                $session_msg = 'Invalid session id.';
            }

        } else {

            // if not show an error

            $session_msg = "You didn't claim any \$DESK before the sessions started.";
        }
    }
}

// from here, the regular operation may continue

if(!isset($_REQUEST['session']) && isset($_REQUEST['Code'])){

    if(trim($_REQUEST['Code']) == '' || trim($_REQUEST['address']) == ''){

        exit('Please enter your Consensus code address and enable your wallet.');
    }

    $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`Code`) = ? And `address` = ''");
    $stmt->execute([strtolower($_REQUEST['Code'])]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if(count($result) != 0){

       //If the code has NOT been used
       // ...additionally if the code as NOT been used only for non-session entries

      $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`address`) = ? And `Code` Not Like 'sess%' LIMIT 11");
      $stmt->execute([strtolower($_REQUEST['address'])]);
      $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if(count($result) < 11){

        $stmt = $pdo->prepare("Update `coindeskemails` Set `address` = ?, `signature` = ? Where Lower(`Code`) = ?");
        $stmt->execute([$_REQUEST['address'], strtolower($_REQUEST['signature']), $_REQUEST['Code']]);

        $arr = array(
          'type'=>'success',
          'title'=>'Congratulations',
          'text'=>'You successfully claimed your tokens! Your balance will updated in a few seconds.'
        );
        exit(json_encode($arr));

      }else{
        $arr = array(
          'type'=>'warning',
          'title'=>'Something went wrong',
          'text'=>'Only 10 codes can be redeemed with the same wallet address.'
        );
        exit(json_encode($arr));
      }

    }else{
      //Enters if the code HAS been used


        $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`address`) = ?");
        $stmt->execute([strtolower($_REQUEST['address'])]);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if(count($result) != 0){

          $arr = array(
            'type'=>'warning',
            'title'=>'Something went wrong',
            'text'=>'This unique code has already been used to claim $DESK. Please try again.'
          );
          exit(json_encode($arr));
        }
        else{

          $arr = array(
            'type'=>'warning',
            'title'=>'Something went wrong',
            'text'=>'Your code has not been found in our database or has been claimed already by someone else. Also make sure to enable your wallet and try again.'
          );
          exit(json_encode($arr));
        }
    }

  }
  ?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content='maximum-scale=1.0, initial-scale=1.0, width=device-width' name='viewport'>
  <title>Claim</title>
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">
  <link href="assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
  <script type="text/javascript" src="js/web3.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/handlebars.js"></script>
  <script type="text/javascript" src="js/ipfs.js"></script>
  <script type="text/javascript" src="js/buffer.js"></script>
  <script type="text/javascript" src="js/nifABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/erc1155ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/multiBatchABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/genesisABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/farmABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/farmShopABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/erc20ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/univ2ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/lib.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/coindesk-dapp.js"></script>
  <script type="text/javascript" src="js/coindesk-init.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/mobile-detection.js"></script>
  <script type="text/javascript" src="js/cookie.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/core/popper.min.js" type="text/javascript"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="assets/js/plugins/chartist.min.js"></script>
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <script src="assets/js/material-dashboard.js?v=2.1.0" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>
  <script>
    $(document).ready(function () {
      new ClipboardJS('.btn-clipboard');
    });
  </script>
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fork-awesome@1.1.7/css/fork-awesome.min.css"
    integrity="sha256-gsmEoJAws/Kd3CjuOQzLie5Q3yshhvmo7YNtBG7aaEY=" crossorigin="anonymous">



  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap');

    .navbar,
    .navbar-text a{
      color: #d8d8d8;
    }
  </style>
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Round|Material+Icons+Outlined" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {

          $("#termsConditions").prop('checked',false).on("click", () => submitBtn());
          $("#privacyPolicy").prop('checked',false).on("click", () => submitBtn());

          function submitBtn(){
            if($("#termsConditions").is(":checked") && $("#privacyPolicy").is(":checked")){
              $("#form-submit").css("opacity", "1");
              $("#form-submit").css("cursor", "pointer");
            }
            else{
              $("#form-submit").css("opacity", "0.5");
              $("#form-submit").css("cursor", "not-allowed");
            }
          }      

            $("#form-submit").on('click', async function(){

              if(!$("#termsConditions").is(":checked") || !$("#privacyPolicy").is(":checked")){
                return;
              }

                let code = <?php echo $session && isset($_REQUEST['Code']) ? json_encode($_REQUEST['Code']) : '$("#code").val()'  ?>;

                if(code == "")
                {
                    alert("Please enter your Concensus code.");

                } else {
                  if (typeof tncLib === 'undefined') {
                      toastr.remove();
                      toastr["warning"]("There seems to be an issue with Torus. Make sure it is enabled, you are logged in and then refresh.", "Torus error");
                    }
                    console.log('calling ajax with address: ', tncLib.account);

                    let signature = await web3.eth.personal.sign("Code: " + code, tncLib.account, "")

                    $.ajax(
                        {
                            url: 'redeem.php',
                            method: 'POST',
                            data: {
                                Code: code,
                                address : tncLib.account,
                                signature: signature
                            },
                            dataType: 'json',
                            success: function(res){

                              toastr.remove();
                              toastr[res.type](res.text, res.title);

                              $("#code").val('');
                            },
                            error: function(err){
                              console.log("Error with database: ")
                              console.log(err)
                            }
                        }
                    )
                }
            });
            <?php
            if($session || isset($_REQUEST['session'])){
            ?>
            let waitForAccount = setInterval(
                function(){
                    if(typeof tncLib != 'undefined'){
                        clearInterval(waitForAccount);
                        <?php
                        if(isset($_REQUEST['session']) && !isset($_REQUEST['address'])) {
                            echo 'location.href = "redeem.php?session="+'.json_encode($_REQUEST['session']).'+"&address="+tncLib.account;';
                        }else{
                        ?>
                        if(<?php echo json_encode($session_msg); ?> == ''){
                            $("#form-submit").click();
                        }
                        else{
                            $('#thank-you-message').html(<?php echo json_encode($session_msg); ?>);
                        }
                        $('#session-thank-you').css('display', 'flex');
                        <?php
                        }
                        ?>
                    }
                },
                100
            );
            <?php
            }
            ?>
        });
    </script>

</head>

<body class="light-edition">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="logo">
      <a class="simple-text logo-normal" href="index.html">
        <img src="assets/img/consensus-coindesk.svg" />
        <div id="bscLogo" class="text-muted" style="display: none; font-size: 0.7em;">
          Binance Smart Chain
        </div>
      </a>
    </div>

    <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="material-icons-round">home</i>
              <p>HOME</p>
            </a>
          </li>
          <li class="nav-item" id="marketNavLink">
            <a class="nav-link" href="market.html">
              <i class="material-icons-round">storefront</i>
              <p>NFT MARKET (Live May 20)</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="collectibles.html">
              <i class="material-icons-round">account_balance_wallet</i>
              <p>My NFTs</p>
            </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="redeem.php">
              <i class="material-icons-round">monetization_on</i>
              <p>CLAIM</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="learn-more.html">
              <i class="material-icons-round">help_center</i>
              <p>LEARN MORE</p>
            </a>
          </li>
        </ul>

        <div class="sidebar-divider"></div>

        <div class="sidebar-bottom">
          <div class="sidebar-text">

            <div class="sidebar-text icons">
              <span class="navbar-text">
                <a href="#" target="_blank">
                  <i class="fa fa-telegram"></i>
                </a>
                <a href="#" target="_blank">
                  <i class="fa fa-twitter"></i>
                </a>
                <a href="mailto:desk@coindesk.com" target="_blank">
                  <i class="fa fa-envelope"></i>
                </a>
              </span>
            </div>

            <div class="sidebar-text rights">
              <span>
                Get in touch
              </span>
            </div>

          </div>
        </div>
  
    </div>
  </div>
  <!-- SIDEBAR END -->

  <div class="main-panel">

    <!-- NAV TOGGLER -->
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " id="navigation-example">
      <div class="container-fluid">                
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
          aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
          <span class="sr-only">Toggle navigation</span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
        </button>
      </div>
    </nav>
    <!-- NAV TOGGLER END -->

    <div class="content">

      <!-- HEADER -->
      <div class="custom-header">

        <div class="custom-header__text">
          <div class="custom-header__title">
            <img src="assets/img/desk-logo-title.svg" />
          </div>
          <div class="content__welcome-text">
            <p>
              Enter your custom code from your email to claim your $DESK. 
            </p>
            <h5>
            <strong>Note: </strong>The $DESK token acts as a reward to incentivize use of CoinDesk products and services. It is not intended to have, nor should be construed as having, any direct monetary value.
            </h5>
          </div>
        </div>

        <div id="balance-container" style="display: none;">
          <h3>$DESK Balance</h3>
          <div class="balance-total">
            <img class="balance-total__icon" src="assets/img/desk-logo-balance.svg" />
            <h2 id="wallet-balance"></h2>
          </div>
        </div>

      </div>
      <!-- HEADER END -->

        <div class="container-fluid"<?php echo $session ? ' style="display:none;"' : '';?>>
            <div class="card">
                <div class="card-body claim-form">
                    <form id="redemptionForm" method="post" action="redeem.php" onsubmit="return false;">

                        <div class="form-group">
                            <label for="code" class="form-label">Enter Code:</label>
                            <input type="code" class="form-control" id="code" aria-describedby="codeHelp"/>
                        </div>

                        <label class="checkbox" for="termsConditions">
                          <input type="checkbox" id="termsConditions" name="terms" unchecked>
                          <div class="check" ></div>
                          <label>I have read and accept the <a href="https://www.coindesk.com/desk-terms" target="_blank">Terms and Conditions</a>.</label>
                        </label>

                        <label class="checkbox" for="privacyPolicy">
                          <input type="checkbox" id="privacyPolicy" name="privacy" unchecked>
                          <div class="check" ></div>
                          <label>I have read and accept the <a href="https://www.coindesk.com/desk-privacy-policy" target="_blank">Privacy Policy</a>.</label>
                        </label>


                        <button id="form-submit" type="button" class="btn btn-primary" data-dismiss="modal">Claim $DESK</button>
                    </form>
                </div>
            </div>

            <!-- <hr /> -->
        </div>

        <div class="container-fluid" id="session-thank-you" style="display: none;">
            <div class="card">
                <div class="card-body claim-form">
                    <span id="thank-you-message" style="font-size: 1.4rem;">
                        Please follow your wallet's instructions and wait for the notification of your claim request.
                    </span>
                </div>
            </div>

            <!-- <hr /> -->
        </div>

        <div class="modal fade" style="overflow-y: scroll;" id="coindeskSignupModal" tabindex="-1" aria-labelledby="coindeskLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg ">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="coindeskLabel">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body redeem-modal-content">
              </div>

              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-cancel" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>


    </div>

    <!-- ALERT -->

    <div class="modal fade" style="overflow-y: scroll;" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="alertModalLabel">Info</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body modal-non-nft-content">
          </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
      </div>
    </div>

    <!--Mobile warning popup-->
    <div  id="mobileDetectContainer" style="display: none;">
      <div id="mobileHeader">
        <img src="./assets/img/consensus-small-logo.svg" alt="consensus-coindesk">
        <span id="mobile-close" class="material-icons-outlined">
          close
        </span>
      </div>

      <div id="mobile-content">
        <img src="./assets/img/desktop-warning-icon.svg" alt="desktop-warning-icon">
        <p>
          The site is not optimized for mobile and is best to re-enter the site on desktop instead
        </p>
      </div>      
    </div>

            <!-- MODALS END -->

  <!-- FOOTER -->
  <div class="footer">
    <ul>
      <li>
        <div class="unifty-contacts">
          <div class="unifty-contacts__icons">
            <a href="https://t.me/unifty" target="_blank">
              <i class="fa fa-telegram"></i>
            </a>
            <a href="https://discord.gg/5ZBTgnAd9s" target="_blank">
              <i class="fa fa-discord"></i>
            </a>
            <a href="https://twitter.com/unifty_io" target="_blank">
              <i class="fa fa-twitter"></i>
            </a>
          </div>
          <div class="unifty-contacts__text">
            Unifty Development d.o.o.  |  All rights reserved.
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://unifty.io/" target="_blank">
          built with <img src="favicon-32x32-unifty.png"><span style="font-size: inherit;" href="www.unifty.io"> Unifty </span>
        </a>
      </li>
      <li class="nav-item" id="torus">
        <a class="nav-link" href="javascript:enableTorus();">
          <img src="assets/img/torus-logo.svg" />
        </a>
      </li>      
    </ul>
  </div>
  <!-- FOOTER END -->

  </div>
  
</body>

</html>