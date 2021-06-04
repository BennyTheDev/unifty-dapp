function TncDapp() {

    const ipfs = window.IpfsHttpClient('ipfs.infura.io', '5001', { protocol: 'https' });
    const _this = this;
    this.marketTemplate = Handlebars.compile($('#market-template').html());
    this.nextButtonTemplate = Handlebars.compile($('#next-button').html());
    this.noMarketsTemplate = Handlebars.compile($('#no-markets').html());
    this.lastIndex = -1;
    this.currentBlock = 0;
    this.prevAccounts = [];
    this.prevChainId = '';

    this.populateMyMarkets = async function(){

        if(_this.lastIndex == -1) {
            $('#marketsPage').html('');
        }else{
            $('#loadMore').remove();
        }

        let length = await window.tncLibCustomMarket.getMyMarketsLength();

        console.log('Markets Length: ', length);

        let offset = _this.lastIndex > -1 ? _this.lastIndex : length;
        let currentIndex = offset;

        let explorer = 'https://etherscan.io/address/';
        switch(chain_id){
            case '4':
                explorer = 'https://rinkeby.etherscan.io/address/';
                break;
            case '61':
                explorer = 'https://testnet.bscscan.com/address/';
                break;
            case '38':
                explorer = 'https://bscscan.com/address/';
                break;
            case '4d':
                explorer = 'https://blockscout.com/poa/sokol/address/';
                break;
            case '507':
                explorer = 'https://moonbeam-explorer.netlify.app/address/';
                break;
            case 'a4ec':
                explorer = 'https://explorer.celo.org/address/';
                break;
            case 'a86a': // avalanche
                explorer = 'https://cchain.explorer.avax.network/address/';
                break;
            case '64':
                explorer = 'https://blockscout.com/poa/xdai/address/';
                break;
            case '89':
                explorer = 'https://explorer.matic.network/address/';
                break;
        }

        for(let i = offset - 3; i >= 0; i = i - 3){
            currentIndex = i;
            let market = await window.tncLibCustomMarket.getMyMarket(i);

            let _uri = market.uri.replace('ipfs://','https://gateway.ipfs.io/ipfs/').replace('/ipfs/ipfs/', '/ipfs/');

            try {

                let data = await $.getJSON(_uri);

                console.log(data);

                if (typeof data == 'object') {

                    let tmpl = _this.marketTemplate({
                        explorer : explorer,
                        currency: getCurrency(),
                        image: data.image.replace('ipfs://','https://gateway.ipfs.io/ipfs/').replace('/ipfs/ipfs/', '/ipfs/'),
                        name: data.name,
                        description: data.description,
                        url : window.location.origin  + window.location.pathname + "market-view.html?address=" + market.wrapperAddress,
                        index: i,
                        index2: i*2,
                        address: market.wrapperAddress
                    });

                    $('#marketsPage').append(tmpl);

                    $('.currency').html(getCurrency());

                    $(".popover-description").popover({
                        trigger: "manual",
                        html: true,
                        animation: false
                    }).on("mouseenter", function() {
                        var _this = this;
                        $(this).popover("show");
                        $(".popover").on("mouseleave", function() {
                            $(_this).popover('hide');
                        });
                    }).on("mouseleave", function() {
                        var _this = this;
                        setTimeout(function() {
                            if (!$(".popover:hover").length) {
                                $(_this).popover("hide");
                            }
                        }, 300);
                    });
                }

            }catch (e){

                console.log('Trouble resolving market uri: ', _uri);
            }

            let maxPerLoad = 9;
            let currInvertedIndex = (length - 1) - i;

            if( currInvertedIndex % maxPerLoad == maxPerLoad - 1 ){

                _this.lastIndex = i;

                break;
            }
        }

        if(currentIndex > 0){

            $('#loadMore').remove();
            $('#marketsPage').append(_this.nextButtonTemplate({}));
            $('#loadMoreButton').off('click');
            $('#loadMoreButton').on('click', function(){
                _this.populateMyMarkets();
            });
        }

        if( length == 0 ){

            $('#marketsPage').html(_this.noMarketsTemplate({}));
        }

    };

    this.newMarket = async function(){

        let name = $('#marketName').val().trim();
        let tier = parseInt($('#marketTier').val().trim());
        let description = $('#marketDescription').val().trim();
        let controller = $('#marketFeeAddress').val().trim();
        let marketFee = $('#marketFee').val().trim();
        let marketSwapFee = $('#marketSwapFee').val().trim();
        let image = $('#marketImageUrl').val().trim();
        let twitter = $('#marketTwitter').val().trim();
        let discord = $('#marketDiscord').val().trim();
        let telegram = $('#marketTelegram').val().trim();
        let medium = $('#marketMedium').val().trim();
        let instagram = $('#marketInstagram').val().trim();
        let youtube = $('#marketYoutube').val().trim();
        let web = $('#marketWeb').val().trim();
        let email = $('#marketEmail').val().trim();
        let phone = $('#marketPhone').val().trim();
        let link = $('#marketCustomLink').val().trim();
        let text = $('#marketCustomLinkText').val().trim();

        if(name == ''){ _alert('Please enter a farm name'); return; }
        if(controller == ''){ _alert('Please enter a controller address'); return; }
        if(!await web3.utils.isAddress(controller)){ _alert('Invalid controller address'); return; }
        if(isNaN(parseFloat(marketFee))){ _alert('Please enter a valid sales fee.'); return; }
        if(isNaN(parseFloat(marketSwapFee))){ _alert('Please enter a valid swap fee.'); return; }
        if(parseFloat(marketFee) < 0){ _alert('Please enter a valid sales fee.'); return; }
        if(parseFloat(marketSwapFee) < 0){ _alert('Please enter a valid swap fee.'); return; }

        let marketInfo = {
            name : name,
            description : description,
            image : image,
            twitter : twitter,
            discord: discord,
            telegram: telegram,
            medium: medium,
            instagram: instagram,
            youtube : youtube,
            web : web,
            email : email,
            phone : phone,
            customLink : { name : text, value : link }
        };

        console.log(JSON.stringify(marketInfo));

        ipfs.add(buffer.Buffer(JSON.stringify(marketInfo)), async (err, result) => {

            console.log(err, result);

            let marketJsonUrl = "https://gateway.ipfs.io/ipfs/" + result[0].hash;

            _this.pin(result[0].hash);

            toastr.remove();

            marketFee = _this.resolveNumberString(""+(Number(marketFee).toFixed(2)), 2);
            marketSwapFee = _this.resolveNumberString(""+(Number(marketSwapFee).toFixed(2)), 2);

            let staking = await tncLibCustomMarket.getStakingAmounts();
            let stakingAmount = 0;

            console.log("Available staking amounts: ", staking);

            switch(tier){
                case 2:
                    stakingAmount = staking.tier2;
                    break;
                case 3:
                    stakingAmount = staking.tier3;
                    break;
                default:
                    stakingAmount = staking.tier1;
            }

            console.log("staking amount: ", stakingAmount.toString());

            let nif = web3.utils.toBN(stakingAmount.toString(), 18);

            let allowance = web3.utils.toBN( await tncLib.allowanceErc20Raw(
                tncLib.nif.options.address,
                tncLib.account,
                tncLibCustomMarket.genesis.options.address
            ) );

            let stakingEnabled = await tncLibCustomMarket.isStakingEnabled();

            if(stakingEnabled){

                let balance = web3.utils.toBN(await tncLib.balanceOfErc20Raw(tncLib.nif.options.address, tncLib.account));

                if( balance.lt(stakingAmount) ){

                    _alert('Not enough $NIF available for staking based on your tier level ('+(await web3.utils.fromWei(stakingAmount.toString()+""))+' $NIF required for Tier ' + tier + ')');
                    return;
                }
            }

            if( stakingEnabled && allowance.lt(nif)  ){

                $('#marketSubmit').prop('disabled', true);
                $('#marketSubmit').html('Approve first!');

                await window.tncLib.approveErc20(
                    tncLib.nif.options.address,
                    nif.toString(),
                    tncLibCustomMarket.genesis.options.address,
                    function () {
                        toastr["info"]('Please wait for the transaction to finish.', "Approve....");
                    },
                    function (receipt) {
                        console.log(receipt);
                        toastr.remove();
                        toastr["success"]('Transaction has been finished.', "Success");
                        $('#marketSubmit').prop('disabled', false);
                        $('#marketSubmit').html('Create Market');
                    },
                    function () {
                        toastr.remove();
                        toastr["error"]('An error occurred with your approval transaction.', "Error");
                        $('#marketSubmit').prop('disabled', false);
                        $('#marketSubmit').html('Create Market');
                    });

            } else {

                tncLibCustomMarket.newMarket(
                    controller,
                    marketFee,
                    marketSwapFee,
                    tier,
                    marketJsonUrl,
                    stakingEnabled,
                    function () {
                        toastr["info"]('Please wait for the transaction to finish.', "New Market....");
                    },
                    function (receipt) {
                        console.log(receipt);
                        toastr.remove();
                        toastr["success"]('Transaction has been finished.', "Success");

                        _this.lastIndex = -1;
                        _this.populateMyMarkets();
                    },
                    function (err) {
                        toastr.remove();
                        let errMsg = 'An error occurred with your New Market transaction. Do you have sufficient funds?';
                        toastr["error"](errMsg, "Error");
                    }
                );
            }
        });
    };

    document.getElementById('marketImageFile').onchange = function () {

        _this.storeIpfsImage( document.getElementById('marketImageFile'), document.getElementById('marketImageUrl') );
    };

    /*
    document.getElementById('marketInfoImageFile').onchange = function () {

        _this.storeIpfsImage( document.getElementById('marketInfoImageFile'), document.getElementById('marketInfoImageUrl') );
    };*/

    this.storeIpfsImage = function(fileElement, urlStorageElement){

        $('.submitNewUpdate').prop('disabled', true);
        let tmp = $('.submitNewUpdate').html();
        $('.submitNewUpdate').html('Uploading Image...');

        let reader = new FileReader();
        reader.onloadend = function () {

            const buf = buffer.Buffer(reader.result);

            ipfs.add(buf, (err, result) => {

                console.log(err, result);

                let ipfsLink = "https://gateway.ipfs.io/ipfs/" + result[0].hash;
                $(urlStorageElement).val(ipfsLink);
                $('.imageFileDisplay').html('<img src=' + JSON.stringify(ipfsLink) + ' border="0" width="200"/>');
                $('.submitNewUpdate').prop('disabled', false);
                $('.submitNewUpdate').html(tmp);

                _this.pin(result[0].hash);
            });
        };

        if (fileElement.files[0]) {
            reader.readAsArrayBuffer(fileElement.files[0]);
        }
    };

    this.resolveNumberString = function(number, decimals){

        let splitted = number.split(".");
        if(splitted.length == 1 && decimals > 0){
            splitted[1] = '';
        }
        if(splitted.length > 1) {
            let size = decimals - splitted[1].length;
            for (let i = 0; i < size; i++) {
                splitted[1] += "0";
            }
            number = "" + (splitted[0] == 0 ? '' : splitted[0]) + splitted[1];
            if(parseInt(number) == 0){
                number = "0";
            }
        }

        return number;
    };

    this.formatNumberString = function (string, decimals) {

        let pos = string.length - decimals;

        if(decimals == 0) {
            // nothing
        }else
        if(pos > 0){
            string = string.substring(0, pos) + "." + string.substring(pos, string.length);
        }else{
            string = '0.' + ( "0".repeat( decimals - string.length ) ) + string;
        }

        return string
    };

    this.pin = function(ipfsToken){

        $.getScript("https://ipfs2arweave.com/permapin/" + ipfsToken)
            .done(function( script, textStatus ) {
                console.log( "PINNED!" );
                console.log( textStatus );
            })
            .fail(function( jqxhr, settings, exception ) {
                console.log( jqxhr, settings, exception );
            });
    }

    $(document).ready(async function(){

        $('#marketFeeAddress').val(tncLib.account);

        if(!await tncLibCustomMarket.isStakingEnabled()){

            $('#feeMarket').css('display','block');
            $('#tierGroup').css('display','none');

            let fee = await web3.utils.fromWei(await tncLibCustomMarket.feeEth()+"");
            $('#ethFee').html(fee);

            let minNif = await web3.utils.fromWei(await tncLibCustomMarket.getMinimumNif()+"");
            if(minNif > 0){
                $('#nifMinMarket').html(minNif);
                $('#paymentDescription').css('display','block');
            }
        }
        else
        {
            $('#stakeMarket').css('display','block');
            let amounts = await tncLibCustomMarket.getStakingAmounts();
            let tier1 = await web3.utils.fromWei(amounts.tier1.toString()+"");
            let tier2 = await web3.utils.fromWei(amounts.tier2.toString()+"");
            let tier3 = await web3.utils.fromWei(amounts.tier3.toString()+"");
            $('#tier1Nif').html(tier1);
            $('#tier2Nif').html(tier2);
            $('#tier3Nif').html(tier3);
        }

        $('#marketSubmit').on('click', async function(){

            await _this.newMarket();

        });

        await web3.eth.subscribe("newBlockHeaders", async (error, event) => {
            if (!error) {
                return;
            }
            console.log(error);
        });
    });

    this.loadPage = async function (page){

        $('#marketsPage').css('display', 'none');

        switch (page){

            default:

                _this.lastIndex = -1;

                $('#marketsPage').css('display', 'grid');
                await _this.populateMyMarkets();

                break;
        }
    };

    this.getUrlParam = function(param_name) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        return urlParams.get(param_name);
    };

    this.startBlockCounter = function(){

        const _this2 = this;

        let startTime = new Date();

        setInterval(async function(){

            const currBlock = await tncLib.getBlock();

            if(parseInt(currBlock) !== parseInt(_this2.currentBlock)){
                const endTime = new Date();
                let timeDiff = endTime - startTime;
                timeDiff /= 1000;
                const seconds = Math.round(timeDiff);
                startTime = new Date(); // restart
            }

            const _endTime = new Date();
            let _timeDiff = _endTime - startTime;
            _timeDiff /= 1000;
            const _seconds = Math.round(_timeDiff);

            if(_seconds > 60 * 5){

                startTime = new Date(); // restart

                console.log("no change in 5 minutes, restarting web3");

                if (window.ethereum) {
                    window.web3 = new Web3(ethereum);
                    try {
                        // Request account access if needed
                        if(typeof ethereum.enable == 'function' && ethereum.enable){

                            await ethereum.enable();
                        }

                        let accounts = [];

                        if(ethereum && typeof ethereum.enable != 'undefined' && ethereum.enable){
                            accounts = await web3.eth.getAccounts();
                            console.log('account classic with ethereum');
                        }
                        else if(ethereum && ( typeof ethereum.enable == 'undefined' || !ethereum.enable ) ){
                            accounts = await window.ethereum.request({
                                method: 'eth_requestAccounts',
                            });
                            console.log('account new with ethereum');
                        }else{
                            accounts = await web3.eth.getAccounts();
                            console.log('account classic without ethereum');
                        }

                        tncLib.account = accounts[0];

                    } catch (error) {
                        console.log(error);
                        _alert('You rejected to use this dapp.');
                    }
                }
                // Legacy dapp browsers...
                else if (window.web3) {
                    if(typeof  window.web3 == 'undefined' || !window.web3) {
                        window.web3 = new Web3(web3.currentProvider);
                    }
                }
            }

            _this.currentBlock = currBlock;

        }, 1000);
    };

    this.accountChangeAlert = function(){
        _alert('Account has been changed. Please <button class="btn btn-primary" onclick="location.reload()">click here</button> to reload this dapp.');
    };

    this.chainChangeAlert = function(){
        _alert('The network has been changed. Please <button class="btn btn-primary" onclick="location.reload()">click here</button> to reload this dapp.');
    };

    this.startAccountCheck = function(){

        if(window.ethereum){

            window.ethereum.on('accountsChanged', function(accounts){
                const _that = _this;
                if (accounts.length != _that.prevAccounts.length || accounts[0].toUpperCase() != _that.prevAccounts[0].toUpperCase()) {
                    _that.accountChangeAlert();
                    _that.prevAccounts = accounts;
                }
            });

        }else if(window.web3){

            setInterval( function() {
                web3.eth.getAccounts(function(err, accounts){
                    const _that = _this;
                    if (accounts.length != 0 && ( accounts.length != _that.prevAccounts.length || accounts[0].toUpperCase() != _that.prevAccounts[0].toUpperCase())) {
                        _that.accountChangeAlert();
                        _that.prevAccounts = accounts;
                    }
                });
            }, 1000);
        }
    };

    this.startChainCheck = function(){

        if(window.ethereum) {
            window.ethereum.on('chainChanged', async function (chain) {
                let actualChainId = chain.toString(16);
                console.log('chain check: ', actualChainId + " != " + _this.prevChainId);
                if (actualChainId != _this.prevChainId) {
                    _this.prevChainId = actualChainId;
                    _this.chainChangeAlert();
                }
            });

        }else if(window.web3){

            setInterval( async function() {

                if(await web3.eth.net.getId() != _this.prevChainId){
                    _this.prevChainId = await web3.eth.net.getId();
                    _this.chainChangeAlert();
                }

            }, 1000);
        }
    };
}

function run(connected) {

    $(document).ready(async function() {

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "100",
            "hideDuration": "1000",
            "timeOut": "0",
            "extendedTimeOut": "0",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        let accounts = [];

        if(typeof ethereum != 'undefined' && ethereum && typeof ethereum.enable != 'undefined' && ethereum.enable){
            accounts = await web3.eth.getAccounts();
            console.log('account classic with ethereum');
        }
        else if(typeof ethereum != 'undefined' && ethereum && ( typeof ethereum.enable == 'undefined' || !ethereum.enable ) ){
            accounts = await window.ethereum.request({
                method: 'eth_requestAccounts',
            });
            console.log('account new with ethereum');
        }else{
            accounts = await web3.eth.getAccounts();
            console.log('account classic without ethereum');
        }

        window.tncLib = new TncLib();
        tncLib.account = accounts[0];

        window.tncLibCustomMarket = new TncLibCustomMarket();
        tncLibCustomMarket.account = accounts[0];

        if(typeof accounts == 'undefined' || accounts.length == 0){

            tncLib.account = '0x0000000000000000000000000000000000000000';
            tncLibCustomMarket.account = '0x0000000000000000000000000000000000000000';
        }

        let dapp = new TncDapp();
        window.tncDapp = dapp;
        dapp.prevAccounts = accounts;
        if(window.ethereum){
            let chain = await web3.eth.getChainId();
            let actualChainId = chain.toString(16);
            dapp.prevChainId = actualChainId;
        }
        else if(window.web3){
            dapp.prevChainId = await web3.eth.net.getId();
        }
        if(window.torus){
            $('#torusAddress').css('display', 'inline-block')
            $('#torusAddressPopover').data('content', tncLib.account);
            $('#torusAddressPopover').popover();
            $('#torusAddressPopover').on('click', function(){
                let input = document.createElement("textarea");
                input.value = tncLib.account;
                document.body.appendChild(input);
                input.select();
                document.execCommand("Copy");
                input.remove();
            })
        }
        dapp.startAccountCheck();
        dapp.startChainCheck();
        //dapp.startBlockCounter();
        dapp.loadPage(''); // default
    });
}