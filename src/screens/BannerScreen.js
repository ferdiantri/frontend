import React, { Component } from 'react';
import { WebView } from 'react-native-webview';

export default function BannerScreen({ navigation, route }){
  const { id_banner } = route.params;
    return (
      <WebView
        source={{
          uri: `http://192.168.43.22:8080/detail_banner_user/${id_banner}`
        }}
        useWebKit={true}
        startInLoadingState={false}
        javaScriptEnabled
        domStorageEnabled
        style={{ flex: 1 }}
      />
    );
  }
  