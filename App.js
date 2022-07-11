import React, { useEffect } from 'react'
import { StatusBar, LogBox, Alert, View, ToastAndroid } from 'react-native'
import axios from 'axios';
import { Provider } from 'react-native-paper'
import { NavigationContainer } from '@react-navigation/native'
import { createStackNavigator } from '@react-navigation/stack'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { theme } from './src/core/theme';
import NetInfo from "@react-native-community/netinfo";
import {
  // StartScreen,
  LoginScreen,
  RegisterScreen,
  ResetPasswordScreen,
  // Dashboard,
  BarangScreen,
  DetailBarangScreen,
  KeranjangScreen,
  BeliSekarangScreen,
  ProfileScreen,
  PembelianScreen,
  AlamatScreen,
  PembayaranScreen,
  PesananBelumDibayarScreen,
  PesananDibayarScreen,
  PesananDalamProsesScreen,
  PesananSedangDikirimScreen,
  PesananTerkirimScreen,
  SemuaPesananScreen,
  DetailPesananScreen,
  UploadScreen,
  SearchBarangScreen,
  ResultSearchBarangScreen,
  BannerScreen,
  KomplainScreen,
  PengisianKomplainScreen,
  PesananDikomplainScreen,
  DetailKomplainScreen
} from './src/screens'

LogBox.ignoreLogs([
  "[react-native-gesture-handler] Seems like you\'re using an old API with gesture components, check out new Gestures system!",
]);
LogBox.ignoreLogs(['VirtualizedLists should never be nested']);

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();

function Net() {
  return (
    ToastAndroid.show('Error', LONG)
  )
}
function HomeTabs() {
    return (
      <Tab.Navigator 
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: '#00aced',
        // tabBarActiveBackgroundColor: '#e3e3e3',
        tabBarHideOnKeyboard: true,
        tabBarInactiveTintColor: 'black',
        tabBarShowLabel: false,
        tabBarStyle: {
            position: 'absolute',
            bottom: 0,
            left: 0,
            elevation: 0,
            flex: 1,
            height: 50
            }
        }}
      // tabBarOptions={{
      //   fontSize: 12,
      //   keyboardHidesTabBar: true
      // }}
      >
        <Tab.Screen name="Home" component={BarangScreen} options={{tabBarIcon: ({ color, size }) => (
            <Icon name="home" size={30} color={color}/>
          ),}}/>
        <Tab.Screen name="Keranjang" component={KeranjangScreen} options={{tabBarIcon: ({ color, size }) => (
            <Icon name="cart" size={30} color={color} />
          ),}} />
        <Tab.Screen name="Profile" component={ProfileScreen} options={{tabBarIcon: ({ color, size }) => (
            <Icon name="account" size={30} color={color} />
          ),}}/>
      </Tab.Navigator>
    );
  }

  NetInfo.refresh().then(state => {
    console.log("Connection type", state.type);
    console.log("Is connected?", state.isConnected);
    if(state.isConnected == false){
      Net();
    }
});
  
  export default function App() {
    useEffect(() => {
      async function sync(){
          NetInfo.refresh().then(state => {
              console.log("Tipe Koneksi", state.type);
              console.log("Koneksi Terhubung?", state.isConnected);
              if(state.isConnected == false){
                  ToastAndroid.show('Koneksi tidak terhubung', ToastAndroid.LONG);           
              }
              else{
                  const headers = {
                      headers: {
                          'Accept': 'application/json',
                          'Content-Type': 'application/json',
                      }
                  }
                  axios.get(`http://192.168.43.22:8000/api/user/sync`, headers).then(response => {
                  console.log(response.data.success)
                  })
              }
          })
      }
    
      // setInterval(() => sync({ time: Date.now() }), 120000)
    },[]);
    return (
        <Provider theme={theme}>
        <NavigationContainer>
          <Stack.Navigator
            initialRouteName="BarangScreen"
            screenOptions={{
              headerShown: false,
            }}
          >
            {/* <Stack.Screen name="StartScreen" component={StartScreen} /> */}
            <Stack.Screen name="LoginScreen" component={LoginScreen} />
            <Stack.Screen name="RegisterScreen" component={RegisterScreen} />
            {/* <Stack.Screen name="Dashboard" component={Dashboard} /> */}
            <Stack.Screen name="BarangScreen" component={HomeTabs} />
            <Stack.Screen name="SearchBarangScreen" component={SearchBarangScreen} />
            <Stack.Screen name="ResultSearchBarangScreen" component={ResultSearchBarangScreen} />
            <Stack.Screen name="DetailBarangScreen" component={DetailBarangScreen} />
            <Stack.Screen name="KeranjangScreen" component={HomeTabs} />
            <Stack.Screen name="BeliSekarangScreen" component={BeliSekarangScreen} />
            <Stack.Screen name="PembelianScreen" component={PembelianScreen} />
            <Stack.Screen name="ProfileScreen" component={ProfileScreen} />
            <Stack.Screen name="AlamatScreen" component={AlamatScreen} />
            <Stack.Screen name="PembayaranScreen" component={PembayaranScreen} />
            <Stack.Screen name="PesananBelumDibayarScreen" component={PesananBelumDibayarScreen} />
            <Stack.Screen name="PesananDibayarScreen" component={PesananDibayarScreen} />
            <Stack.Screen name="PesananSedangDikirimScreen" component={PesananSedangDikirimScreen} />
            <Stack.Screen name="PesananDalamProsesScreen" component={PesananDalamProsesScreen} />
            <Stack.Screen name="PesananTerkirimScreen" component={PesananTerkirimScreen} />
            <Stack.Screen name="SemuaPesananScreen" component={SemuaPesananScreen} />
            <Stack.Screen name="DetailPesananScreen" component={DetailPesananScreen} />
            <Stack.Screen name="UploadScreen" component={UploadScreen} />
            <Stack.Screen name="BannerScreen" component={BannerScreen} />
            <Stack.Screen name="KomplainScreen" component={KomplainScreen} />
            <Stack.Screen name="PengisianKomplainScreen" component={PengisianKomplainScreen} />
            <Stack.Screen name="PesananDikomplainScreen" component={PesananDikomplainScreen} />
            <Stack.Screen name="DetailKomplainScreen" component={DetailKomplainScreen} />
            <Stack.Screen
              name="ResetPasswordScreen"
              component={ResetPasswordScreen}
            />
          </Stack.Navigator>
        </NavigationContainer>
      </Provider>
    );
  }