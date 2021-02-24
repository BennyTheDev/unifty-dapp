function TncLibMarket(){

    this.getUrlParam = function(param_name) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        return urlParams.get(param_name);
    };

    // ETHEREUM RINKEBY
    if(chain_id === "4") {

        this.market = new web3.eth.Contract(marketABI, '0x7503539b4c684682CbE0C8A0746e124c6b803162', {from: this.account});
        this.account = '';

        // xDAI MAINNET
    } else if(chain_id === "64") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

        // xDAI / POA (Sokol) TESTNET
    } else if(chain_id === "4d") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

        // Matic
    } else if(chain_id === "89") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

        // BINANCE TESTNET
    } else if(chain_id === "61") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

        // Moonbase Alpha
    } else if(chain_id === "507") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

        // CELO
    } else if(chain_id === "a4ec") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

    } else if(chain_id === "38") {

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

    } else{

        this.market = new web3.eth.Contract(marketABI, '', {from: this.account});
        this.account = '';

    }

    this.setAccount = function(address){
        this.account = address;
    };

    this.onSale = async function(account, erc1155Address, nftId){

        await sleep(sleep_time);
        return await this.market.methods.onSale(account, erc1155Address, nftId).call({from:this.account});
    };

    this.sell = async function(erc1155Address, nftId, amount, token, pricePerItem, preCallback, postCallback, errCallback){

        await sleep(sleep_time);

        let gas = 0;

        try{

            gas = await this.market.methods.sell(erc1155Address, nftId, amount, token, pricePerItem).estimateGas({
                from:this.account,
            });

        }catch(e){
            console.log(e);
            errCallback("");
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.market.methods.sell(erc1155Address, nftId, amount, token, pricePerItem)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback();
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                console.log("Sell order placed.");
                postCallback(receipt);
            });
    };

}