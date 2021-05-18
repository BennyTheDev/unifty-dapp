function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function detectDivScroll(elem, resolver) {
  let height = $(elem).get(0).scrollHeight - $(elem).height();
  let scroll = $(elem).scrollTop();

  let isScrolledToEnd = scroll + 150 >= height;
  if (isScrolledToEnd) {
    resolver("done");
  }
}

function waitForDiv(elem, itemCount) {
  $(elem).off("scroll");
  return new Promise(function (resolve, reject) {
    detectDivScroll(elem, resolve);
    $(elem).on("scroll", function () {
      detectDivScroll(elem, resolve);
    });
  });
}

function waitForPaging(pageId, itemCount) {
  $(window).off("scroll");
  return new Promise(function (resolve, reject) {
    if (itemCount % 8 == 0) {
      let nearToBottom = 450;
      if (
        $(window).scrollTop() + $(window).height() >=
        $(document).height() - nearToBottom
      ) {
        resolve("done");
      }
    } else if (itemCount % 8 == 7) {
      $(window).on("scroll", function () {
        let nearToBottom = 450;
        if (
          $(window).scrollTop() + $(window).height() >=
          $(document).height() - nearToBottom
        ) {
          resolve("done");
        }
      });
    } else {
      resolve("done");
    }
  });
}

enableTorus();

function enableTorus() {
  $.getScript("https://unpkg.com/@toruslabs/torus-embed").done(
    async function () {
      let chain = "0";
      let networkName = "";

      switch (chain_id) {
        case "4":
          chain = "rinkeby";
          networkName = "Rinkeby";
          break;
        case "64":
          chain = "https://xdai1-rpc.unifty.cloud";
          networkName = "xDai";
          break;
        case "89":
          chain = "matic";
          networkName = "Polygon";
          break;
        case "38":
          chain = "https://bsc1-rpc.unifty.cloud";
          networkName = "Binance Smart Chain";
          break;
        case "a86a":
          chain = "https://api.avax.network/ext/bc/C/rpc";
          networkName = "Avalanche";
          break;
        case "1":
          chain = "mainnet";
          networkName = "Mainnet";
          break;
      }

      if (chain != "0") {
        // block the screen
        $("body").append(
          $(
            '<div id="uxOverlay" style="display:none;"><div><img src="assets/img/cd-loader-80px.gif"></div></div>'
          ).css("display", "flex")
        );

        const torus = new Torus({
          buttonPosition: "bottom-right", // default: bottom-left
        });

        await torus.init({
          buildEnv: "production", // default: production
          enableLogging: true, // default: false
          network: {
            host: chain, // default: mainnet
            chainId: chain_id, // default: 1
            networkName: networkName, // default: Main Ethereum Network
          },
          showTorusButton: true, // default: true
        });

        $("#torus a").css("display", "none");
        localStorage.setItem("torusLoaded", "true");

        await torus.login(); // await torus.ethereum.enable()
        window.web3 = new Web3(torus.provider);
        window.torus = torus;

        // release block of screen
        $("#uxOverlay").css("display", "none");
        run(true);
      } else {
        runReadableOnly();
      }
    }
  );
}

function runReadableOnly() {
  if (
    chain_id == "89" ||
    chain_id == "38" ||
    chain_id == "64" ||
    chain_id == "4" ||
    chain_id == "1" ||
    chain_id == "a4ec" ||
    chain_id == "a86a"
  ) {
    let rpcUrl = "";

    switch (chain_id) {
      case "a86a":
        rpcUrl = "https://api.avax.network/ext/bc/C/rpc";
        break;
      case "a4ec":
        rpcUrl = "https://forno.celo.org/";
        break;
      case "89":
        rpcUrl =
          "https://holy-weathered-glade.matic.quiknode.pro/9c7e1575f6d450d4fceeffde6b2e5ed69a3eed13/";
        break;
      case "38":
        rpcUrl = "https://bsc1-rpc.unifty.cloud/";
        break;
      case "64":
        rpcUrl = "https://xdai1-rpc.unifty.cloud/";
        break;
      case "4":
        rpcUrl =
          "https://rinkeby.infura.io/v3/fb5477e6dc7145b8a89f4296d78c500a";
        break;
      default:
        rpcUrl =
          "https://mainnet.infura.io/v3/ba2d61d52e4246fd8d58a64e2f754d48";
    }

    window.web3 = new Web3(new Web3.providers.HttpProvider(rpcUrl));

    run(true);
  } else {
    _alert("Please install a wallet like Metamask to use this dapp.");
  }
}
