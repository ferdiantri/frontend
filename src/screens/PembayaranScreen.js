import React, { Component, useEffect } from 'react';
import { WebView } from 'react-native-webview';
import { BackHandler } from 'react-native';


export default function PembayaranScreen({ navigation, route }){
  function handleBackButtonClick() {
    navigation.popToTop();
    return true;
  }
  useEffect(() => {
    BackHandler.addEventListener("hardwareBackPress", handleBackButtonClick);
    return () => {
      BackHandler.removeEventListener("hardwareBackPress", handleBackButtonClick);
    };
  }, []);
  const { link_invoice } = route.params;
    return (
      <WebView
        source={{
          uri: link_invoice
        }}
        useWebKit={true}
        startInLoadingState={false}
        javaScriptEnabled
        domStorageEnabled
        style={{ flex: 1 }}
      />
    );
  }
  