function TncLibDao(){

    this.getUrlParam = function(param_name) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        return urlParams.get(param_name);
    };

    // ETHEREUM RINKEBY
    if(chain_id === "4") {

        this.dao = new web3.eth.Contract(daoABI, '0x5A658ea1a22576e42ff255f6d1f88c6fF91a3A22', {from: this.account});
        this.account = '';

    } else{

        this.account = '';

    }

    this.setAccount = function(address){
        this.account = address;
    };

    this.earnedConsumer = async function(consumer, account){
        await sleep(sleep_time);
        let con = new web3.eth.Contract(daoConsumerABI, consumer, {from: this.account});
        return await con.methods.earned(account).call({from:this.account});
    }

    this.peerUri = async function(consumer, peer){
        await sleep(sleep_time);
        let con = new web3.eth.Contract(daoConsumerABI, consumer, {from: this.account});
        return await con.methods.peerUri(peer).call({from:this.account});
    }

    this.frozen = async function(account){
        await sleep(sleep_time);
        return await this.dao.methods.frozen(account).call({from:this.account});
    };

    this.minNifStake = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.minNifStake().call({from:this.account});
    };

    this.consumerCounter = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.consumerCounter().call({from:this.account});
    };

    this.consumers = async function(id){
        await sleep(sleep_time);
        return await this.dao.methods.consumers(id).call({from:this.account});
    };

    this.consumerInfo = async function(id){
        await sleep(sleep_time);
        return await this.dao.methods.consumerInfo(id).call({from:this.account});
    };

    this.votesCounter = async function(pid){
        await sleep(sleep_time);
        return await this.dao.methods.votesCounter(pid).call({from:this.account});
    };

    this.votes = async function(pid, id){
        await sleep(sleep_time);
        return await this.dao.methods.votes(pid, id).call({from:this.account});
    };

    this.addressProposalInfo = async function(pid){
        await sleep(sleep_time);
        return await this.dao.methods.addressProposalInfo(pid).call({from:this.account});
    };

    this.uint256ProposalInfo = async function(pid){
        await sleep(sleep_time);
        return await this.dao.methods.uint256ProposalInfo(pid).call({from:this.account});
    };

    this.getProposal = async function(i){
        await sleep(sleep_time);
        return await this.dao.methods.proposals(i).call({from:this.account});
    };

    this.earnedUnt = async function(consumer){
        await sleep(sleep_time);
        return await this.dao.methods.earnedUnt(consumer).call({from:this.account});
    };

    this.voted = async function(_id, _account){
        await sleep(sleep_time);
        return await this.dao.methods.voted(_id, _account).call({from:this.account});
    };

    this.isPausing = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.isPausing().call({from:this.account});
    };

    this.graceTime = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.graceTime().call({from:this.account});
    };

    this.proposalExecutionLimit = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.proposalExecutionLimit().call({from:this.account});
    };

    this.proposalCount = async function(){
        await sleep(sleep_time);
        return await this.dao.methods.proposalCounter().call({from:this.account});
    };

    this.accountInfo = async function(account){
        await sleep(sleep_time);
        return await this.dao.methods.accountInfo(account).call({from:this.account});
    };

    this.stake = async function(funds, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.stake(funds).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.stake(funds)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.unstake = async function(preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.unstake().estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.unstake()
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeAddConsumer = async function(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeAddConsumer(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeAddConsumer(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeUpdateConsumerGrant = async function(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeUpdateConsumerGrant(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeUpdateConsumerGrant(_consumer, _sizeUnt, _rateSeconds, _startTime, _duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeConsumerWhitelistPeer = async function(_consumer, _peer,_duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeConsumerWhitelistPeer(_consumer, _peer,_duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeConsumerWhitelistPeer(_consumer, _peer,_duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeRemoveConsumer = async function(_consumer,_duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeRemoveConsumer(_consumer,_duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeRemoveConsumer(_consumer,_duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeConsumerRemovePeerFromWhitelist = async function(_consumer, _peer,_duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeConsumerRemovePeerFromWhitelist(_consumer, _peer,_duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeConsumerRemovePeerFromWhitelist(_consumer, _peer,_duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeGeneral = async function(_duration, _url, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.proposeGeneral(_duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.proposeGeneral(_duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.proposeNumeric = async function(_type, _value, _duration, _url, preCallback, postCallback, errCallback){

        _type = 'propose' + _type[0].toUpperCase() + _type.slice(1);

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods[_type](_value, _duration, _url).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods[_type](_value, _duration, _url)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.vote = async function(_proposalId, _supporting, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.vote(_proposalId, _supporting).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.vote(_proposalId, _supporting)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.execute = async function(_proposalId, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.execute(_proposalId).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.execute(_proposalId)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.allocate = async function(consumer, peer, preCallback, postCallback, errCallback){

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await this.dao.methods.allocate(consumer, peer).estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        this.dao.methods.allocate(consumer, peer)
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };

    this.withdraw = async function(consumer, preCallback, postCallback, errCallback){

        let con = new web3.eth.Contract(daoConsumerABI, consumer, {from: this.account});

        let gas = 0;

        try {
            await sleep(sleep_time);
            gas = await con.methods.withdraw().estimateGas({
                from:this.account
            });
        }catch(e){
            console.log(e.message);
            errCallback(e);
            return;
        }

        const price = await web3.eth.getGasPrice();

        con.methods.withdraw()
            .send({
                from:this.account,
                gas: gas + Math.floor( gas * 0.1 ),
                gasPrice: Number(price) + Math.floor( Number(price) * 0.1 )
            })
            .on('error', async function(e){
                errCallback(e);
            })
            .on('transactionHash', async function(transactionHash){
                preCallback();
            })
            .on("receipt", function (receipt) {
                postCallback(receipt);
            });
    };
}