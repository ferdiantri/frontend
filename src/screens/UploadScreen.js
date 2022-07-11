
import React, {useState} from 'react';
import EncryptedStorage from 'react-native-encrypted-storage'
// Import required components
import {
  SafeAreaView,
  StyleSheet,
  Text,
  View,
  TouchableOpacity,
  Image,
  Platform,
  PermissionsAndroid,
  ToastAndroid,
  Alert,
} from 'react-native';
import { Card, Button } from 'react-native-paper'
import axios from 'axios';
const FormData = global.FormData;
import {
  launchCamera,
  launchImageLibrary
} from 'react-native-image-picker';
import Icon from "react-native-vector-icons/Feather";
// import RNFetchBlob from 'rn-fetch-blob'
 
export default function UploadScreen ({navigation, route}){
  const { profile_picture } = route.params;
  const [filePath, setFilePath] = useState();
  const [changeNumber, setChangeNumber] = useState(0);
 
  const requestCameraPermission = async () => {
    if (Platform.OS === 'android') {
      try {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.CAMERA,
          {
            title: 'Camera Permission',
            message: 'App needs camera permission',
          },
        );
        // If CAMERA Permission is granted
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      } catch (err) {
        console.warn(err);
        return false;
      }
    } else return true;
  };
 
  const requestExternalWritePermission = async () => {
    if (Platform.OS === 'android') {
      try {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.WRITE_EXTERNAL_STORAGE,
          {
            title: 'External Storage Write Permission',
            message: 'App needs write permission',
          },
        );
        // If WRITE_EXTERNAL_STORAGE Permission is granted
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      } catch (err) {
        console.warn(err);
        alert('Write permission err', err);
      }
      return false;
    } else return true;
  };
 
  const captureImage = async (type) => {
    let options = {
      mediaType: type,
      maxWidth: 300,
      maxHeight: 300,
      quality: 1,
      saveToPhotos: true,
      includeBase64: true
    };
    let isCameraPermitted = await requestCameraPermission();
    let isStoragePermitted = await requestExternalWritePermission();
    if (isCameraPermitted && isStoragePermitted) {
      launchCamera(options, (response) => {
        console.log('Response = ', response.assets);
        if (response.didCancel) {
          ToastAndroid.show('Pengguna membatalkan operasi', ToastAndroid.LONG)
          return;
        } else if (response.errorCode == 'camera_unavailable') {
          ToastAndroid.show('Perangkat tidak mendukung', ToastAndroid.LONG)
          return;
        } else if (response.errorCode == 'permission') {
          ToastAndroid.show('Pengguna tidak mengizinkan', ToastAndroid.LONG)
          return;
        } else if (response.errorCode == 'others') {
          alert(response.errorMessage);
          return;
        }
        console.log('base64 -> ', response.assets.map(item => item.base64));
        console.log('uri -> ', response.assets.map(item => item.uri));
        console.log('width -> ', response.assets.map(item => item.width));
        console.log('height -> ', response.assets.map(item => item.height));
        console.log('fileSize -> ', response.assets.map(item => item.fileSize));
        console.log('type -> ', response.assets.map(item => item.type));
        console.log('fileName -> ', response.assets.map(item => item.fileName));
        setFilePath(response.assets.map(item => item.uri)[0]);
      });
    }
  };
 
  const chooseFile = (type) => {
    let options = {
      mediaType: type,
      maxWidth: 300,
      maxHeight: 300,
      quality: 1,
    };
    launchImageLibrary(options, (response) => {
      console.log('Response = ', response.assets);
      
      if (response.didCancel) {
        ToastAndroid.show('Pengguna membatalkan operasi', ToastAndroid.LONG)
        return;
      } else if (response.errorCode == 'camera_unavailable') {
        ToastAndroid.show('Perangkat tidak mendukung', ToastAndroid.LONG)
        return;
      } else if (response.errorCode == 'permission') {
        ToastAndroid.show('Pengguna tidak mengizinkan', ToastAndroid.LONG)
        return;
      } else if (response.errorCode == 'others') {
        alert(response.errorMessage);
        return;
      }
      console.log('base64 -> ', response.assets.map(item => item.base64));
      console.log('uri -> ', response.assets.map(item => item.uri));
      console.log('width -> ', response.assets.map(item => item.width));
      console.log('height -> ', response.assets.map(item => item.height));
      console.log('fileSize -> ', response.assets.map(item => item.fileSize));
      console.log('type -> ', response.assets.map(item => item.type));
      console.log('fileName -> ', response.assets.map(item => item.fileName));
      setFilePath(response.assets[0].uri);
    });
  };
  async function changeImage(){
    setChangeNumber(1);
  }
  async function uploadImage(){
    if(!filePath){
        console.log("Tidak Boleh Kosong");
    }
    else{
        const token = await EncryptedStorage.getItem("token");
        if(token == null){
            navigation.replace('LoginScreen');
        }
        else{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                  'Authorization': 'Bearer '+token
                }
            }
            try{
              axios.get('http://192.168.43.22:8000/api/user/profile', headers).then(response => {
                const config = {
                  headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'multipart/form-data'
                  }
                }
                const formData = new FormData()
                  formData.append('email', response.data.profile.email)
                  formData.append('gambar', {
                      uri: filePath.toString(),
                      type: 'image/jpeg',
                      name: 'photo.jpg'
                  })
                  axios({
                    method: 'post',
                    url: 'http://192.168.43.22:8000/api/user/ubah_foto_profile',
                    data: formData,
                  })
                // const upload = {
                //   uri : filePath.replace('file:///', 'file://'),
                //   type : 'image/jpeg'
                // }
                // console.log(upload)
                
                // const body = new FormData();
                //   body.append('gambar', filePath, 'test.jpg')
                //   body.append('email',response.data.profile.email);
                // console.log(body);

                // axios({
                //     url    : 'http://192.168.43.22:8000/api/user/ubah_foto_profile',
                //     method : 'POST',
                //     data   : body,
                //     headers: {
                //     Accept: 'application/json',
                //     'Content-Type': 'multipart/form-data',
                //     },
                // })
                // fetch(
                //   'http://192.168.43.22:8000/api/user/ubah_foto_profile',
                //   {
                //     body: body,
                //     method: "POST",
                //     headers: {
                //       'Content-Type': 'multipart/form-data',
                //     }
                //   }
                // )
                // RNFetchBlob.fetch('POST', 'http://192.168.43.22:8000/api/user/ubah_foto_profile', {
                //   otherHeader : "foo",
                //   'Content-Type' : 'multipart/form-data',
                // }, [
                //   // element with property `filename` will be transformed into `file` in form data
                //   { name : 'gambar', data: upload},
                //   // custom content type
                //   { name : 'email', data: response.data.profile.email},
                //   // part file from storage
                // ])
                .then(response => {
                  if(response.data.success){
                    Alert.alert(response.data.success);
                    setChangeNumber(0);
                  }
                  if(response.data.error){
                    Alert.alert(response.data.success);
                    setChangeNumber(1);
                  }
                })
              })
            }
            catch(err){
              console.log(err)
            }
        }
      }
    }
  return (
      <View style={{flex: 1}}>
        {
          profile_picture == 'data_gambar/profile_picture.png' || changeNumber == 1 ?
            <View style={styles.container}>
              {filePath == null ?
                <View style={{flexDirection: 'row'}}>
                    <View style={{flex: 1, alignItems: 'center'}}>
                        <TouchableOpacity onPress={() => captureImage('photo')}>
                            <Icon name='camera' size={70} color='black'/>
                            <Text style={styles.textStyle}>Kamera</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{flex: 1, alignItems: 'center'}}>
                        <TouchableOpacity onPress={() => chooseFile('photo')}>
                            <Icon name='image' size={70} color='black'/>
                            <Text style={styles.textStyle}>Gambar</Text>
                        </TouchableOpacity>
                    </View>
                </View>
                :
                <SafeAreaView style={{flex: 1}}>
                    <View style={styles.container}>
                      <Image source={{ uri: filePath}} style={{width: 300, height: 300, borderRadius: 50}} />
                    </View>
                    <View>
                      <View style={{marginHorizontal: 10, marginTop: 10}}>
                        <Button mode="contained" onPress={() => uploadImage()}>Unggah</Button>
                      </View>
                    </View>
                </SafeAreaView>
              }
            </View>
            :
            <SafeAreaView style={{flex: 1}}>
              <View style={styles.container}>
                <Image source={{ uri: 'http://192.168.43.22:8000/' + profile_picture }} style={{width: 300, height: 300, borderRadius: 50}} />
              </View>
              <View>
                <View style={{marginHorizontal: 10, marginVertical: 10}}>
                  <Button mode="contained" onPress={() => changeImage()}>Ubah Foto</Button>
                </View>
              </View>
            </SafeAreaView>
        }
        
    </View>
  );
};

 
const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 10,
    backgroundColor: '#fff',
    alignItems: 'center',
  },
  titleText: {
    fontSize: 22,
    fontWeight: 'bold',
    textAlign: 'center',
    paddingVertical: 20,
  },
  textStyle: {
    padding: 10,
    color: 'black',
    textAlign: 'center',
  },
  buttonStyle: {
    alignItems: 'center',
    backgroundColor: '#DDDDDD',
    padding: 5,
    marginVertical: 10,
    width: 250,
  },
  imageStyle: {
    width: 200,
    height: 200,
    margin: 5,
  },
});