<template>
  <div id="app" ref="root"></div>
</template>

<script>
import { ZegoUIKitPrebuilt } from '@zegocloud/zego-uikit-prebuilt';

function randomID(len) {
  let result = '';
  if (result) return result;
  var chars = '12345qwertyuiopasdfgh67890jklmnbvcxzMNBVCZXASDQWERTYHGFUIOLKJP',
    maxPos = chars.length,
    i;
  len = len || 5;
  for (i = 0; i < len; i++) {
    result += chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return result;
}

export function getUrlParams(
  url = window.location.href
) {
  let urlStr = url.split('?')[1];
  return new URLSearchParams(urlStr);
}

export default {
  name: 'App',
  components: {},
  mounted() {
    const roomID = getUrlParams().get('roomID');
    const userName = getUrlParams().get('userName') || randomID(5);
    // generate Kit Token
    const appID = 591798701;
    const serverSecret = "ef2324e49ea8c00e7faa3a7f947c5080";
    const kitToken =  ZegoUIKitPrebuilt.generateKitTokenForTest(appID, serverSecret, roomID,  randomID(5),  userName);

    
    // Create instance object from Kit Token.
    const zp = ZegoUIKitPrebuilt.create(kitToken);
    // start the call
    zp.joinRoom({
        container: this.$refs.root,
        sharedLinks: [
          {
            name: 'Lien pour les étudiants',
            url:
              window.location.protocol + '//' +
              window.location.host + window.location.pathname +
              '?roomID=' +
              roomID,
          },
        ],
        scenario: {
         mode: ZegoUIKitPrebuilt.VideoConference,
        },
        showRecordingButton: true, 
    });
  },
};
</script>

<style>
#app {
  height: 100vh;
  width: 100vw;
}
</style>
