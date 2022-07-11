import React, { useState } from 'react'
import { View, StyleSheet, TouchableOpacity, Alert, ToastAndroid, ImageBackground } from 'react-native'
import { Text } from 'react-native-paper'
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Button from '../components/Button'
import TextInput from '../components/TextInput'
import BackButton from '../components/BackButton'
import { theme } from '../core/theme'
import { emailValidator } from '../helpers/emailValidator'
import { passwordValidator } from '../helpers/passwordValidator'
import { ulangiPasswordValidator } from '../helpers/ulangiPasswordValidator'
import { namaValidator } from '../helpers/namaValidator'
import { nomorTeleponValidator } from '../helpers/nomorTeleponValidator'
import Axios from 'axios'
import { ScrollView } from 'react-native-gesture-handler'

export default function RegisterScreen({ navigation }) {
  const [nama, setNama] = useState({ value: '', error: '' })
  const [nomor_telepon, setNomorTelepon] = useState({ value: '', error: '' })
  const [email, setEmail] = useState({ value: '', error: '' })
  const [password, setPassword] = useState({ value: '', error: '' })
  const [ulangiPassword, setUlangiPassword] = useState({ value: '', error: '' })

  const onSignUpPressed = () => {
    const namaError = namaValidator(nama.value)
    const emailError = emailValidator(email.value)
    const nomorTeleponError = nomorTeleponValidator(nomor_telepon.value)
    const passwordError = passwordValidator(password.value)
    const ulangiPasswordError = ulangiPasswordValidator(ulangiPassword.value)
    if (emailError || passwordError || namaError || nomorTeleponError) {
      setNama({ ...nama, error: namaError })
      setEmail({ ...email, error: emailError })
      setNomorTelepon({ ...nomor_telepon, error: nomorTeleponError })
      setPassword({ ...password, error: passwordError })
      setUlangiPassword({ ...ulangiPassword, error: ulangiPasswordError })
      return
    }
    if(ulangiPassword.value != password.value){
      ToastAndroid.show('Password Dan Ulangi Password Tidak Sama', ToastAndroid.LONG);
    }
    else{
      let headers = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
      }
      let data = {
        nama : nama.value,
        email : email.value,
        nomor_telepon : nomor_telepon.value,
        password: password.value
      }
      try{
        Axios.post('http://192.168.43.22:8000/api/user/signup', data, headers).then(response => {
          console.log(response.data);
          if(response.data.error){
            Alert.alert(response.data.error[0]);
          }
          else if(response.data){
            navigation.reset({
              index: 0,
              routes: [{ name: 'LoginScreen' }],
            })
          }
        });
      }
      catch(err){
        console.log(err);
      }
    }
  }

  return (
    <View style={{flex: 1}}>
      <ImageBackground source={require('../assets/background.png')} style={{flex: 1, width: '100%',}}>
        <ScrollView style={{marginVertical: 40}}>
          <View style={{justifyContent: 'center', alignItems:'center'}}>
            <Logo />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center'}}>
            <Header>Selamat Datang</Header>
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <TextInput
              label="Nama Lengkap"
              returnKeyType="next"
              value={nama.value}
              onChangeText={(text) => setNama({ value: text, error: '' })}
              error={!!nama.error}
              errorText={nama.error}
              theme={{ roundness: 20 }}
            />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <TextInput
              label="Email"
              returnKeyType="next"
              value={email.value}
              onChangeText={(text) => setEmail({ value: text, error: '' })}
              error={!!email.error}
              errorText={email.error}
              autoCapitalize="none"
              autoCompleteType="email"
              textContentType="emailAddress"
              keyboardType="email-address"
              theme={{ roundness: 20 }}
              />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <TextInput
              label="Nomor Telepon"
              returnKeyType="next"
              value={nomor_telepon.value}
              onChangeText={(text) => setNomorTelepon({ value: text, error: '' })}
              error={!!nomor_telepon.error}
              errorText={nomor_telepon.error}
              theme={{ roundness: 20 }}
            />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <TextInput
              label="Password"
              returnKeyType="done"
              value={password.value}
              onChangeText={(text) => setPassword({ value: text, error: '' })}
              error={!!password.error}
              errorText={password.error}
              secureTextEntry
              theme={{ roundness: 20 }}
            />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <TextInput
            label="Ulangi Password"
            returnKeyType="done"
            value={ulangiPassword.value}
            onChangeText={(text) => setUlangiPassword({ value: text, error: '' })}
            error={!!ulangiPassword.error}
            errorText={ulangiPassword.error}
            secureTextEntry
            theme={{ roundness: 20 }}
          />
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40}}>
            <Button
              mode="contained"
              onPress={onSignUpPressed}
              style={{ marginTop: 24 }}
            >
              Sign Up
            </Button>
          </View>
          <View style={{justifyContent: 'center', alignItems:'center', marginHorizontal: 40, flexDirection: 'row'}}>
          <Text>Sudah mempunyai akun? </Text>
          <TouchableOpacity onPress={() => navigation.replace('LoginScreen')}>
            <Text style={styles.link}>Login</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </ImageBackground>
  </View>
  )
}

const styles = StyleSheet.create({
  link: {
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
})
