import React, { useEffect, useState } from 'react'
import { Text } from 'react-native'
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Paragraph from '../components/Paragraph'
import Button from '../components/Button'
import axios from 'axios'
import EncryptedStorage from 'react-native-encrypted-storage';


export default function Dashboard({ navigation }) {
  const [email, setEmail] = useState();
  useEffect(() => {
    async function getProfile(){
    const token = await EncryptedStorage.getItem("token");
    if(token == null){
      navigation.reset({
        index: 0,
        routes: [{ name: 'StartScreen' }],
      })
    }
    let headers = {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer '+token
      }
     }
    axios.get('http://192.168.43.22:8000/api/user/profile', headers).then(response => {
      console.log(response.data.profile);
      setEmail(response.data.profile.email);
      });
    }
    getProfile()
  },[]);

  async function onLogoutPressed(){
    const token = await EncryptedStorage.getItem("token");
    let headers = {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer '+token
      }
     }
    axios.get('http://192.168.43.22:8000/api/user/logout', headers).then(async(response) => {
      console.log(response.data);
      await EncryptedStorage.removeItem("token");
      if(response.data.success){
        navigation.reset({
          index: 0,
          routes: [{ name: 'StartScreen' }],
        })
      }
      });
  }

  return (
    <Background>
      <Logo />
      <Header>Let’s start</Header>
      <Paragraph>
      {email}
      </Paragraph>
      <Button mode="contained" onPress={onLogoutPressed}>
        Keluar
      </Button>
      <Button
        mode="outlined"
        onPress={() => navigation.navigate('BarangScreen')}
      >Barang</Button>
      <Button
        mode="outlined"
        onPress={() => navigation.navigate('LoginScreen')}
      >Login</Button>
      <Button
        mode="outlined"
        onPress={() => navigation.navigate('PembayaranScreen')}
      >Pembayaran</Button>
    </Background>
  )
}