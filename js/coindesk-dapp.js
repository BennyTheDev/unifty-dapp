function TncDapp() {

    const _this = this;

    this.displayBalance = function(){

        // use tncLib.account to display the balance

        $(document).ready(async function(){
            setInterval(async function(){

                let balance = await tncLib.balanceOfErc20Raw('0xa7814452481532987BEFa93cE55EE7CC60aAb916', tncLib.account);

                console.log(balance);

                //toLocaleString to add commas to thousands in number
                balance = parseInt(_this.formatNumberString(balance, 18)).toLocaleString()

                $('#wallet-balance').text(balance)
                $('#balance-container').show();

            }, 5000);
        });
    }

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

    $(document).ready(async function(){

        $('#myPoolsButton').on('click', function(){
            _this.loadPage('myPools');
        });

        await web3.eth.subscribe("newBlockHeaders", async (error, event) => {
            if (!error) {
                return;
            }
            console.log(error);
        });
    });
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

        if(typeof accounts == 'undefined' || accounts.length == 0){

            tncLib.account = '0x0000000000000000000000000000000000000000';
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
        dapp.displayBalance();
    });
}