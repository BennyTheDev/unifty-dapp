

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function waitForPaging(pageId, itemCount) {
    $(window).off('scroll');
    return new Promise(
        function(resolve, reject){
            if(itemCount % 6 == 0){
                $(window).on('scroll', function(){
                    let nearToBottom = 250;
                    if ($(window).scrollTop() + $(window).height() >
                        $(document).height() - nearToBottom) {
                        resolve('done');
                    }
                });
            }
            else{

                resolve('done');
            }
        }
    );
}

$(document).ready(async function(){

    if (window.ethereum) {

        setTimeout(async function(){

            window.web3 = new Web3(ethereum);

            if(window.ethereum.isTrust){
                _alert('Trust Wallet not supported due to insufficient Web3 support. Please use Metamask or a wallet that fully supports Web3.');
                return;
            }

            try {

                // Request account access if needed

                let chain = await web3.eth.getChainId();
                let actualChainId = chain.toString(16);

                console.log(actualChainId);

                if(actualChainId != chain_id){

                    let desc =  'You are not connected to ' + network + '. Please change to the right network and reload.';

                    if(chain_id == '61'){

                        desc =  'You are not connected to the Binance Smart Chain TESTNET.<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: Binance Smart Chain TESTNET <br/>' +
                            'New RPC URL: https://data-seed-prebsc-1-s1.binance.org:8545/<br/>' +
                            'ChainID: 97<br/>' +
                            'Symbol: BNB<br/>' +
                            'Block Explorer URL: https://testnet.bscscan.com<br /><br/>' +
                            'Get free test BNB from <a href="https://testnet.binance.org/faucet-smart" target="_blank">here</a>.<br/>' +
                            'More information on the setup <a href="https://docs.binance.org/smart-chain/wallet/metamask.html" target="_blank">here</a>.';

                    }else if(chain_id == '38'){

                        desc =  'You are not connected to the Binance Smart Chain MAINNET.<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: Binance Smart Chain<br/>' +
                            'New RPC URL: https://bsc-dataseed4.binance.org/<br/>' +
                            'ChainID: 56<br/>' +
                            'Symbol: BNB<br/>' +
                            'Block Explorer URL: https://bscscan.com<br /><br/>' +
                            'More information on the setup <a href="https://docs.binance.org/smart-chain/wallet/metamask.html" target="_blank">here</a>.';

                    }else if(chain_id == '507'){

                        desc =  'You are not connected to the Moonbase Alpha Testnet (Moonbeam/Polkadot).<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: Moonbase Alpha<br/>' +
                            'New RPC URL: https://rpc.testnet.moonbeam.network<br/>' +
                            'ChainID: 1287<br/>' +
                            'Symbol: DEV<br/>' +
                            'Block Explorer URL: N/A<br /><br/>' +
                            'More information on the setup, including how to receive test DEV <a href="https://docs.moonbeam.network/getting-started/testnet/connect/" target="_blank">here</a>.';

                    }else if(chain_id == 'a4ec'){

                        desc =  'You are not connected to CELO.<br/><br/>' +
                            'Please use the following setup in the CELO Desktop Wallet => Settings => Networks => Add Network: <br /><br />' +
                            '*** Please disable Metamask when using the CELO Desktop Wallet. You can turn it back on at any time but do not run both at the same time. ***<br/><br/>' +
                            'Network Name: CELO<br/>' +
                            'New RPC URL: https://forno.celo.org/<br/>' +
                            'ChainID: 42220<br/>' +
                            'Symbol: CELO<br/>' +
                            'Block Explorer URL: https://explorer.celo.org/<br /><br/>' +
                            'Install the CELO Desktop Wallet from here <a href="https://chrome.google.com/webstore/detail/celo-desktop-wallet/kkilomkmpmkbdnfelcpgckmpcaemjcdh" target="_blank">here</a>.';

                    }else if(chain_id == '4d'){

                        desc =  'You are not connected to the xDAI/POA (Sokol) TESTNET.<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: Sokol TESTNET<br/>' +
                            'New RPC URL: https://sokol.poa.network<br/>' +
                            'ChainID: 77<br/>' +
                            'Symbol: SPOA<br/>' +
                            'Block Explorer URL: https://blockscout.com/poa/sokol<br /><br/>' +
                            'More information on the setup, including how to receive test SPOA <a href="https://www.poa.network/for-developers/getting-tokens-for-tests/sokol-testnet-faucet" target="_blank">here</a>.';

                    }else if(chain_id == '64'){

                        desc =  'You are not connected to the xDAI MAINNET<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: xDAI<br/>' +
                            'New RPC URL: <input type="text" class="w-100" value="https://ancient-wandering-thunder.xdai.quiknode.pro/e63baf3261859fd9a4aaf2dcc42d4ec1535805df/"/><br/>' +
                            'ChainID: 100<br/>' +
                            'Symbol: xDai<br/>' +
                            'Block Explorer URL: https://blockscout.com/poa/xdai<br /><br/>' +
                            'More information on the setup <a href="https://www.xdaichain.com/for-users/wallets/metamask/metamask-setup" target="_blank">here</a>.';

                    }else if(chain_id == '89'){

                        desc =  'You are not connected to Matic MAINNET<br/><br/>' +
                            'Please use the following setup in Metamask => Settings => Networks => Add Network: <br /><br />' +
                            'Network Name: Matic<br/>' +
                            'New RPC URL: https://rpc-mainnet.matic.network<br/>' +
                            'ChainID: 137<br/>' +
                            'Symbol: MATIC<br/>' +
                            'Block Explorer URL: https://explorer.matic.network<br /><br/>' +
                            'More information on the setup <a href="https://docs.matic.network/docs/develop/metamask/config-matic/" target="_blank">here</a>.';

                    }

                    desc += '<br /><br /><button class="btn btn-primary" onclick="location.reload()">Reload</button>';

                    $('#alertModal').find('.modal-dialog').addClass('modal-lg')
                    _alert(desc);

                } else {

                    if(typeof ethereum.enable == 'function' && ethereum.enable){

                        await ethereum.enable();
                    }

                    run(true);
                }

            } catch (error) {
                console.log(error);
                _alert('You refused to use this dapp.');
            }

        }, 500);
    }
    // Legacy dapp browsers...
    else if (window.web3) {

        if(typeof  window.web3 == 'undefined' || !window.web3) {
            window.web3 = new Web3(web3.currentProvider);
        }

        if(await web3.eth.net.getId() != chain_id.toString(16)){

            let desc =  'You are not connected to ' + network;
            desc += '<br /><br /><button class="btn btn-primary" onclick="location.reload()">Try again and reload/button>';

            _alert(desc);
        }

        run(true);
    }
    // Non-dapp browsers...
    else {

        let desc =  'Please install a web wallet like Metamask to use this dapp.<br /><button class="btn btn-primary" onclick="location.reload()">Try again and reload</button>';

        _alert(desc);
    }

});

