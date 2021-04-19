<?php
if(isset($_POST['UserEmail'])){

    if(trim($_POST['UserEmail']) == '' || trim($_POST['address']) == ''){

        exit('Please enter your email address and enable your wallet.');
    }

    $dsn = 'mysql:host=localhost;dbname=unifghiu_coindesk;charset=utf8';
    $usr = 'unifghiu_coindesk';
    $pwd = 'coindesk';
    $pdo = new PDO($dsn, $usr, $pwd);

    $stmt = $pdo->prepare("SELECT `id` FROM `coindeskemails` WHERE Lower(`userEmail`) = ? And `address` = ''");
    $stmt->execute([strtolower($_POST['UserEmail'])]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if(count($result) != 0){

        $stmt = $pdo->prepare("Update `coindeskemails` Set `address` = ? Where Lower(`userEmail`) = ?");
        $stmt->execute([$_POST['address'], strtolower($_POST['UserEmail'])]);

        exit('Verification success!');
    }

    exit('Your email address has not been found or you requested your tokens already.');
}
?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content='maximum-scale=1.0, initial-scale=1.0, width=device-width' name='viewport'>
  <title>Farm Builder - Unifty</title>
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
      color: #8b92a9;
    }
  </style>
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Round" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {

            $("#form-submit").on('click', async function(){

                let email = $("#email").val();

                if(email == "")
                {
                    alert("Please enter your email address.");
                } else{
                    console.log('calling ajax with address: ', tncLib.account);
                    $.ajax(
                        {
                            url: 'redeem.php',
                            method: 'POST',
                            data: {
                                UserEmail: email,
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
      <a href="https://unifty.io" class="simple-text logo-normal">
        <img src="assets/img/unifty2.png" width="125" border="0" />
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
              <p>Home</p>
            </a>
          </li>
          <li class="nav-item" id="marketNavLink">
            <a class="nav-link" href="market.html">
              <i class="material-icons-round">storefront</i>
              <p>NFT Market</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="collectibles.html">
              <i class="material-icons-round">account_balance_wallet</i>
              <p>Your Wallet</p>
            </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="redeem.php">
              <i class="material-icons-round">account_balance_wallet</i>
              <p>Redeem</p>
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
                <i class="material-icons">copyright</i>
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
        <div class="custom-header__title">Unifty.io</div>
        <div id="balance-container" style="display: none;">
          <h3>$DESK Balance</h3>
          <h2 id="wallet-balance"></h2>

        </div>
      </div>
      <!-- HEADER END -->
      
      <div class="content__welcome-text">
        <p>
          Please enable your wallet (Metamask or Torus) and enter your email address to request your tokens.
        </p>
      </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form id="redemptionForm" method="post" action="redeem.php" onsubmit="return false;">

                        <div class="form-group">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" aria-describedby="emailHelp"/>
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>

                        <!--Wallet address: <input type="text" id="address" /><br>-->

                        <button style="font-size: 1.2rem" id="form-submit" type="button" class="btn btn-primary" data-dismiss="modal">Request Tokens</button>
                    </form>
                </div>
            </div>
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
                <div class="buttons-container">
                  <button type="button" class="btn btn-secondary modal-cancel" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>


    </div>

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
        <a class="nav-link" href="faq.html">
          powered by &nbsp<span style="font-size: inherit;" href="www.unifty.io"> Unifty </span><img src="favicon-32x32.png">
      </a>
      </li>
    </ul>

  </div>
  <!-- FOOTER END -->

  </div>
  
</body>

</html>