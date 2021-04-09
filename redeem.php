<?php
  session_start();

  /*
  if(isset($_SESSION['loggedIN'])) {
    header('Location: hidden.php');
    exit();
  }
  */


  if(isset($_POST['UserEmail'])){
    $connection = new mysqli('localhost', 'root', '', 'unifty');

    //Check if connection to db was successful
    if($connection->connect_error) 
      die("Connection failed: ". $connection->connect_error);

    $email = $connection->real_escape_string($_POST['UserEmail']);
    //$email = md5($connection->real_escape_string($_POST['UserEmail']));

    //$address = $connection->real_escape_string($_POST['UserAddress']);
    $currentDateTime = date('Y-m-d H:i:s');


    //Testing if this combor already exists
    $data = $connection->query("SELECT id FROM coindeskemails WHERE userEmail='$email'");

    if($data->num_rows == 0){
      
      $data = $connection->query("INSERT INTO `coindeskemails` (`ID`, `Date`, `UserEmail`) VALUES (NULL, '$currentDateTime', '$email');");

      if($data){
        echo "New record created successfully! ";
        $_SESSION['loggedIN'] = '1';
        $_SESSION['email'] = $email;
      }
      else{
        echo "Error: " . $data . "" . mysqli_error($connection);
        exit('Eror inserting Data into DB.');
      }

      exit('Verification success!');
    } elseif($data->num_rows > 0){
      exit ('This email seems to be in use.');
    } 
    else
      exit('Please check your inputs');

    exit($email . "=" . $address);
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
  <script type="text/javascript" src="js/misc.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/nifABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/erc1155ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/genesisABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/multiBatchABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/farmABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/farmShopABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/erc20ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/univ2ABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/marketABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/bridgeInABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/bridgeOutABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/marketWrapABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/swapABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/uniftyverseABI.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/lib.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/lib-bridge.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/lib-market.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/collectibles-dapp.js?v=1.9.2"></script>
  <script type="text/javascript" src="js/dapp-init.js?v=1.9.2"></script>
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
  </style>
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Round" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet" />

</head>

<body>

  <script src="js/themeDefault.js"></script>


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
        <li class="nav-item active">
          <a class="nav-link" href="collectibles.html">
            <i class="material-icons-round">account_balance_wallet</i>
            <p>Your Wallet</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="redeem.php">
            <i class="material-icons-round">account_balance_wallet</i>
            <p>Redeem</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manager.html">
            <i class="material-icons-round">collections</i>
            <p>Collection Manager</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="farm-builder.html">
            <i class="material-icons-round">build</i>
            <p>Farm Builder</p>
          </a>
        </li>
        <li class="nav-item bridgeNav">
          <a class="nav-link" href="bridge.html">
            <i class="material-icons-round">360</i>
            <p>NFT Bridge</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);" id="nulsSco">
            <i class="material-icons-round">monetization_on</i>
            <p>Get Project Funding</p>
          </a>
        </li>
          <li class="nav-item" id="xdaiFarm">
              <a class="nav-link" href="https://unifty.io/xdai/farm-view.html?address=0x87E5aFba16E3BEbd967699B4D5472B2ae1AA8Fa7">
                  <i class="material-icons">grass</i>
                  <p>Rares Farm - NIF (xDai)</p>
              </a>
          </li>

          <li class="nav-item" id="genesisFarm">
              <a class="nav-link" href="https://unifty.io/farm.html?farm=0xA2c88A39b1DE8f1d723d9E76AD07f6012826d0f3">
                  <i class="material-icons">grass</i>
                  <p>Genesis Farm - NIF-LP</p>
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
        <div class="custom-header__dropdowns">
          <script>
            // fix for initial doubleclick required upon page load
            // $(document).ready(function () {
            //   $('#chainSelectionDropdown').click();
            // });
          </script>

          <div class="dropdown ml-3 mr-3 dropdown--chains">
            <button id="chainSelectionDropdown" class="btn btn-info dropdown-toggle" type="button"
              id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Select chain
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="https://unifty.io/">Ethereum</a>
              <a class="dropdown-item" href="https://unifty.io/xdai/">xDai</a>
              <a class="dropdown-item" href="https://unifty.io/bsc/">Binance Smart Chain</a>
              <a class="dropdown-item" href="https://unifty.io/celo/">Celo</a>
              <a class="dropdown-item" href="https://unifty.io/matic/">Polygon (Matic)</a>
              <!--<a class="dropdown-item" href="https://unifty.io/moonbeam-alpha/">Moonbeam (Alpha/Test)</a>-->
              <a class="dropdown-item" href="https://unifty.io/rinkeby/">Rinkeby (Testnet)</a>
            </div>            
          </div>

          <div class="theme-toggler">
            <label for="themeSwitch" class="theme-options">
              <span class="material-icons-round light-mode">
                light_mode
              </span>
              <span class="material-icons-round dark-mode">
                dark_mode
              </span>
            </label>
            <input type="checkbox" id="themeSwitch" name="themeSwitch" />
          </div>

        </div>
      </div>
      <!-- HEADER END -->
      
      <div class="content__welcome-text">
        <p>
          This is your personal NFT wallet on UNIFTY.
        </p>
      </div>

      <div style="margin: 0 auto; width: 450px;">

        <form id="myForm" method="post" action="">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" aria-describedby="emailHelp">
          <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>

          <!--Wallet address: <input type="text" id="address" /><br>-->

          <input id="form-submit" type="button" value="Send"/>


        </form>

        <p id="response">

        </p>

        <div data-toggle="modal" data-target="#coindeskSignupModal">
          <button>this</button>
        </div>
        
        <script>
          $(document).ready(function() {
            $("#form-submit").on('click', function(){
              var email = $("#email").val();
              
              if(email == "")
                alert("Don't leave any fields empty");
              else{
                $.ajax(
                {
                  url: 'redeem.php',
                  method: 'POST',
                  data: {
                    UserEmail: email
                  },
                  success: function(res){
                    $("#response").html(res);

                    if(res.indexOf('success') >= 0)
                      $('#nftContractModal').modal('show');

                  },
                  dataType: 'text'
                }
              )
              }              
              
            })
          });

        </script>

        <style>
          #myForm{


            padding: 1.5rem;
            margin: 0 auto;

            border-radius: 8px;

            border: solid 1px grey;
          }

          #form-submit{
            font-size: 1.4rem;
            border: solid 1px grey;
            background: #f4f7fb;
            border-radius: .8rem;
            width: 60px;
            padding: 10px;
            margin: 0;
            margin-right: 0px;
          }
        </style>
      </div>


        <div class="modal fade" style="overflow-y: scroll;" id="coindeskSignupModal" tabindex="-1" aria-labelledby="coindeskLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg ">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="coindeskLabel">Congratulations!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body modal-non-nft-content">

                <div class="col">

                  <h2>You have now been registered for the distribution of $DESK</h2>
                  

                </div>
              </div>

              <div class="modal-footer">
                <div id="feeCollection" class="footer-description">
                  If you have any questions please contact <a href="mailto:news@coindesk.com">news@coindesk.com</a>
                </div>
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
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://app.uniswap.org/#/swap?outputCurrency=0x7e291890b01e5181f7ecc98d79ffbe12ad23df9e">
            <span class="material-icons-round">
              find_replace                  
            </span>
            Get $NIF on Uniswap
          </a>
        </li>

        <li class="nav-item shop" id="shopEthereum" style="display:none;">
          <a class="nav-link" href="https://unifty.io/farm-view.html?address=0x7387DF14E182b37aEF944C231E6f4D88EbBa1145">
            <span class="material-icons-round">
              shopping_cart
            </span>
            Shop
          </a>
        </li>

        <li class="nav-item shop" id="shopXdai" style="display:none;">
          <a class="nav-link" href="https://unifty.io/xdai/farm-view.html?address=0xCBe3F53c26A717704d12D219F8B43016BbDa9082">
            <span class="material-icons-round">
              shopping_cart
            </span>
            Shop
          </a>
        </li>

        <li class="nav-item shop" id="shopBsc" style="display:none;">
          <a class="nav-link" href="https://unifty.io/bsc/farm-view.html?address=0x7B717526fF81f28053F82170D96f01B4605A5e90">
            <span class="material-icons-round">
              shopping_cart
            </span>
            Shop
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://unifty.eth.link">
            <span class="material-icons-round">
              present_to_all                
            </span>
            Mirror
          </a>
        </li>


        <li class="nav-item">
          <a class="nav-link" href="roadmap.html">
            <span class="material-icons-round">
              swap_calls
            </span>
            Roadmap
          </a>
        </li>


        <li class="nav-item">
          <a class="nav-link" href="faq.html">
            <span class="material-icons-round">
              help_outline
            </span>
            FAQ
          </a>
        </li>

        <li class="nav-item" style="display:none;" id="torus">
          <a class="nav-link" href="javascript:enableTorus();">
                    <span class="material-icons-round">
                      account_balance_wallet
                    </span>
            Enable Wallet
          </a>
        </li>

        <li class="nav-item" style="display:none;" id="torusAddress">
          <a href="javascript:void(0);" id="torusAddressPopover" type="button" class="btn btn-clipboard" style="cursor: pointer;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" title="Your Address" data-html="true" data-content="">
                      <span class="material-icons-round">
                        account_balance_wallet
                      </span>
          </a>
        </li>
      </ul>

    </div>
    <!-- FOOTER END -->

  </div>

  <script src="js/themeSwitch.js"></script>
</body>

</html>