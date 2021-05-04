<?php
if(isset($_POST['Code'])){

    if(trim($_POST['Code']) == '' || trim($_POST['address']) == ''){

        exit('Please enter your Consensus code address and enable your wallet.');
    }

    $dsn = 'mysql:host=localhost;dbname=unifty;charset=utf8';
    $usr = 'root';
    $pwd = '';
    $pdo = new PDO($dsn, $usr, $pwd);

    $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`Code`) = ? And `address` = ''");
    $stmt->execute([strtolower($_POST['Code'])]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if(count($result) != 0){

      //Checking if the wallet address is being used
      $stmt = $pdo->prepare("SELECT TOP 1 1 FROM `coindeskemails` WHERE `address` = 'address'");
      $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if(count($result) != 0){
        $stmt = $pdo->prepare("Update `coindeskemails` Set `address` = ? Where Lower(`Code`) = ?");
        $stmt->execute([$_POST['address'], strtolower($_POST['Code'])]);

        exit('Verification success!');   
      }

      exit('This wallet address has already received $DESK');
    }

    exit('Your code has not been found or you requested your tokens already.');
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

            $("#form-submit").on('click', async function(){

                let code = $("#code").val();

                if(code == "")
                {
                    alert("Please enter your Concensus code.");
                } else{
                    console.log('calling ajax with address: ', tncLib.account);
                    $.ajax(
                        {
                            url: 'redeem.php',
                            method: 'POST',
                            data: {
                                Code: code,
                                address : tncLib.account
                            },
                            success: function(res){
                                $('.redeem-modal-content').html(res);
                                $('#coindeskSignupModal').modal('show');
                            },
                            dataType: 'text'
                        }
                    )
                }
            })
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
              <i class="material-icons-outlined">storefront</i>
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
                <a href="https://t.me/unifty" target="_blank">
                  <i class="fa fa-telegram"></i>
                </a>
                <a href="https://discord.gg/5ZBTgnAd9s" target="_blank">
                  <i class="fa fa-discord"></i>
                </a>
                <a href="https://twitter.com/unifty_io" target="_blank">
                  <i class="fa fa-twitter"></i>
                </a>
              </span>
            </div>
  
            <div class="sidebar-text rights">
              <span>
                Unifty Development d.o.o.<br/>All rights reserved.
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
          </div>
        </div>

        <div id="balance-container" style="display: none;">
          <h3>$DESK Balance</h3>
          <div class="balance-total">
            <img class="balance-total__icon" src="assets/img/deskcoin.svg" />
            <h2 id="wallet-balance"></h2>
          </div>
        </div>

      </div>
      <!-- HEADER END -->

        <div class="container-fluid">
            <div class="card">
                <div class="card-body claim-form">
                    <form id="redemptionForm" method="post" action="redeem.php" onsubmit="return false;">

                        <div class="form-group">
                            <label for="code" class="form-label">Enter code:</label>
                            <input type="code" class="form-control" id="code" aria-describedby="codeHelp"/>
                            <!-- <div id="codeHelp" class="form-text">Code is redeemable only once.</div> -->
                        </div>

                        <!--Wallet address: <input type="text" id="address" /><br>-->

                        <button id="form-submit" type="button" class="btn btn-primary" data-dismiss="modal">Claim $DESK</button>
                    </form>
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
      <li class="nav-item"id="torus">
        <a class="nav-link" href="javascript:enableTorus();">
                    <span class="material-icons-round">
                      account_balance_wallet
                    </span>
          Torus wallet
        </a>
        <a class="nav-link" href="https://unifty.io/" target="_blank">
          built with <img src="favicon-32x32.png"><span style="font-size: inherit;" href="www.unifty.io"> Unifty </span>
      </a>
      </li>
    </ul>
  </div>
  <!-- FOOTER END -->

  </div>
  
</body>

</html>