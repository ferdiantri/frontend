import React, { useEffect, useState } from "react"
import { SafeAreaView, ScrollView, StyleSheet, Text, View, ActivityIndicator, Alert, TextInput, ToastAndroid } from 'react-native'
import EncryptedStorage from 'react-native-encrypted-storage'
import axios from "axios";
import { Card, Appbar, Button } from "react-native-paper";
import Icon from "react-native-vector-icons/Feather";
import { Picker } from '@react-native-picker/picker';
export default function AlamatScreen({ navigation }){
    const [email, setEmail] = useState([]);
    const [alamat, setAlamat] = useState("");
    const [nomorTelepon, setNomorTelepon] = useState("");
    const [kodePos, setKodePos] = useState("");
    const [province_id, setProvince_id] = useState([]);
    const [provinsi, setProvinsi] = useState("");
    const [kabupaten, setKabupaten] = useState("");
    const [city_id, setCity_id] = useState([]);
    const [selectedProvince_id, setSelectedProvince_id] = useState("");
    const [selectedCity_id, setSelectedCity_id] = useState("");
    const [namaPenerima, setNamaPenerima] = useState("");
    const [kecamatan, setKecamatan] = useState("");
    const [dataAlamat, setDataAlamat] = useState(['']);
    const [loading, setLoading] = useState(true);

    console.log(selectedProvince_id);
    console.log(selectedCity_id);

    async function addAlamat(){
        if(selectedProvince_id == "" || provinsi == "" || city_id == "" || kabupaten == "" || kecamatan == "" ||
        namaPenerima == "" || alamat == "" || nomorTelepon == "" || kodePos == ""){
            Alert.alert('Data Harus Diisi');
        }else{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            let data = {
                province_id : selectedProvince_id,
                provinsi : provinsi,
                city_id : selectedCity_id,
                kabupaten : kabupaten,
                kecamatan : kecamatan,
                nama_penerima : namaPenerima,
                alamat : alamat,
                nomor_telepon : nomorTelepon,
                kode_pos : kodePos,
                email : email
            }
            console.log(data);
            try{
                axios.post('http://192.168.43.22:8000/api/user/tambah_alamat', data, headers).then(response => {
                    if(response.data.error){
                        ToastAndroid.show('Gagal Menambahkan Alamat', ToastAndroid.LONG);
                    }
                    else{
                        ToastAndroid.show('Berhasil Menambahkan Alamat', ToastAndroid.LONG);
                        getProfile()
                    }
                })
            }
            catch(err){
                console.log(err)
            }
        }
    }
    async function changeAlamat(){
        let data = {
                    province_id : selectedProvince_id,
                    provinsi : provinsi,
                    city_id : selectedCity_id,
                    kabupaten : kabupaten,
                    kecamatan : kecamatan,
                    nama_penerima : namaPenerima,
                    alamat : alamat,
                    nomor_telepon : nomorTelepon,
                    kode_pos : kodePos,
                    email : email
                }
                console.log(data);
        if(selectedProvince_id == "" || provinsi == "" || city_id == "" || kabupaten == "" || kecamatan == "" ||
        namaPenerima == "" || alamat == "" || nomorTelepon == "" || kodePos == ""){
            alert('Data Harus Diisi');
        }else{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            let data = {
                province_id : selectedProvince_id,
                provinsi : provinsi,
                city_id : selectedCity_id,
                kabupaten : kabupaten,
                kecamatan : kecamatan,
                nama_penerima : namaPenerima,
                alamat : alamat,
                nomor_telepon : nomorTelepon,
                kode_pos : kodePos,
                email : email
            }
            console.log(data);
            try{
                axios.post('http://192.168.43.22:8000/api/user/ubah_alamat', data, headers).then(response => {
                    if(response.data.error){
                        ToastAndroid.show('Gagal Mengubah Alamat', ToastAndroid.LONG);
                    }
                    else{
                        ToastAndroid.show('Berhasil Mengubah Alamat', ToastAndroid.LONG);
                        getProfile()
                    }
                })
            }
            catch(err){
                console.log(err)
            }
        }
    }
    async function getProfile(){
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
            let api = {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'key': 'f151ce36d48ee772f269a04808984ca8',
                }
            }
            try{
                axios.get('http://192.168.43.22:8000/api/user/profile', headers).then(response => {
                    const email = response.data.profile.email;
                    console.log(email);
                    setEmail(response.data.profile.email);
                    axios.get(`http://192.168.43.22:8000/api/user/alamat?email=${response.data.profile.email}`).then(async(response) => {
                        console.log(response.data);
                        if(response.data.error){
                            setDataAlamat(false);
                        }
                        if(response.data.success){
                            setDataAlamat(response.data.success);
                            if(response.data){
                                setNamaPenerima(response.data.success.map(item => item.nama_penerima).toString());
                                setNomorTelepon(response.data.success.map(item => item.nomor_telepon).toString());
                                setSelectedProvince_id(response.data.success.map(item => item.province_id).toString());
                                setSelectedCity_id(response.data.success.map(item => item.city_id)[0]);
                                setProvinsi(response.data.success.map(item => item.provinsi).toString());
                                setKabupaten(response.data.success.map(item => item.kabupaten).toString());
                                setKecamatan(response.data.success.map(item => item.kecamatan).toString());
                                setAlamat(response.data.success.map(item => item.alamat).toString());
                                setKodePos(response.data.success.map(item => item.kode_pos).toString());
                            }
                            axios.get(`https://api.rajaongkir.com/starter/city?province=${response.data.success.map(item => item.province_id).toString()}`, api).then(async(response) =>{
                                setCity_id(response.data.rajaongkir.results);
                            })
                        }
                    
                    })
                })
                axios.get('https://api.rajaongkir.com/starter/province', api).then(response =>{
                    setProvince_id(response.data.rajaongkir.results);    
                })
                .finally(() => setLoading(false));
            }
            catch(err){
                console.log(err);
            }
        }
    }
    useEffect(() => {
        getProfile()
    },[]);
    return(
          <View style={{flex: 1}}>
              {
                loading == true ?
                <View style={styles.loading}>
                    <ActivityIndicator />
                </View>
                :
                <View>
                {dataAlamat == false ?
                <View>
                    <View>
                        <Text style={{fontSize: 20, color: 'black', marginLeft: 10, marginTop: 10}}>Alamat Pengiriman</Text>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Nama Penerima"
                            style={{color: 'black'}}
                            value={namaPenerima}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setNamaPenerima(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Nomor Telepon" 
                            style={{color: 'black'}}
                            value={nomorTelepon}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setNomorTelepon(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <Picker
                                style={{ height: 40, width: 250, color: 'black' }}
                                selectedValue={selectedProvince_id}                 
                                onValueChange={(itemValue, itemIndex) => {
                                    let api = {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'key': 'f151ce36d48ee772f269a04808984ca8',
                                        }
                                    }
                                    axios.get(`https://api.rajaongkir.com/starter/city?province=${itemValue}`, api).then(async(response) =>{
                                        setCity_id(response.data.rajaongkir.results);
                                        console.log(response.data.rajaongkir)
                                    })
                                if (!itemValue) {
                                return;
                                }
                                setSelectedProvince_id(itemValue)
                                setProvinsi(province_id[itemIndex-1].province)
                            }}>
                            <Picker.Item label="Pilih Provinsi" value="" />              
                                {province_id.sort().map(item => 
                                <Picker.Item
                                    key={item.province_id} 
                                    label={item.province} 
                                    value={item.province_id}
                                    /> )}                    
                            </Picker>
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <Picker
                                style={{ height: 40, width: 250, color: 'black' }}
                                placeholderTextColor="#000" 
                                selectedValue={selectedCity_id}                 
                                onValueChange={(itemValue, itemIndex) => {
                                if (selectedProvince_id == null){
                                    Alert.alert("Pilih Provinsi")
                                }
                                if (!itemValue) {
                                    getCity();
                                return;
                                }
                                setSelectedCity_id(itemValue)
                                setKabupaten(city_id[itemIndex-1].city_name)
                            }}>
                            <Picker.Item sty label="Pilih Kabupaten/Kota" value="" />              
                                {city_id.sort().map(item => <Picker.Item key={item.city_id} label={item.city_name} value={item.city_id} /> )}                    
                            </Picker>
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Kecamatan" 
                            value={kecamatan}
                            onChangeText={(text) => setKecamatan(text)}
                            style={{color: 'black'}}
                            placeholderTextColor="#000" 
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Alamat Lengkap"
                            value={alamat}
                            style={{color: 'black'}}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setAlamat(text)} 
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Kode Pos" 
                            value={kodePos}
                            style={{color: 'black'}}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setKodePos(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Button onPress={() => addAlamat()}>Tambah Alamat</Button>
                    </View>
                </View>
                :
                <View>
                    <View>
                        <Text style={{fontSize: 20, color: 'black', marginLeft: 10, marginTop: 10}}>Alamat Pengiriman</Text>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Nama Penerima"
                            value={namaPenerima}
                            placeholderTextColor="#000"
                            style={{color: 'black'}}
                            onChangeText={(text) => setNamaPenerima(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Nomor Telepon" 
                            value={nomorTelepon}
                            style={{color: 'black'}}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setNomorTelepon(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <Picker
                                style={{ height: 40, width: 250, color: 'black' }}
                                selectedValue={selectedProvince_id}                 
                                onValueChange={(itemValue, itemIndex) => {
                                    let api = {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'key': 'f151ce36d48ee772f269a04808984ca8',
                                        }
                                    }
                                    axios.get(`https://api.rajaongkir.com/starter/city?province=${itemValue}`, api).then(async(response) =>{
                                        setCity_id(response.data.rajaongkir.results);
                                        console.log(response.data.rajaongkir)
                                    })
                                if (!itemValue) {
                                return;
                                }
                                setSelectedProvince_id(itemValue)
                                setProvinsi(province_id[itemIndex-1].province)
                            }}>
                            <Picker.Item label="Pilih Provinsi" value="" />              
                                {province_id.sort().map(item => 
                                <Picker.Item 
                                    key={item.province_id} 
                                    label={item.province} 
                                    value={item.province_id}
                                    /> )}                    
                            </Picker>
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <Picker
                                style={{ height: 40, width: 250, color: 'black' }}
                                selectedValue={selectedCity_id}                 
                                onValueChange={(itemValue, itemIndex) => {
                                if (selectedProvince_id == null){
                                    Alert.alert("Pilih Provinsi")
                                }
                                if (!itemValue) {
                                    getCity();
                                return;
                                }
                                setSelectedCity_id(itemValue)
                                setKabupaten(city_id[itemIndex-1].city_name)
                            }}>
                            <Picker.Item sty label="Pilih Kabupaten/Kota" value="" />              
                                {city_id.sort().map(item => <Picker.Item key={item.city_id} label={item.city_name} value={item.city_id} /> )}                    
                            </Picker>
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Kecamatan" 
                            value={kecamatan}
                            onChangeText={(text) => setKecamatan(text)}
                            style={{color: 'black'}}
                            placeholderTextColor="#000" 
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Alamat Lengkap"
                            value={alamat}
                            style={{color: 'black'}}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setAlamat(text)} 
                            />
                        </Card>
                    </View>
                    <View>
                        <Card style={styles.card}>
                            <TextInput placeholder="Kode Pos" 
                            value={kodePos}
                            style={{color: 'black'}}
                            placeholderTextColor="#000"
                            onChangeText={(text) => setKodePos(text)}
                            />
                        </Card>
                    </View>
                    <View>
                        <Button onPress={() => changeAlamat()}>Ubah Alamat</Button>
                    </View>
                </View>
                }
            </View>
            }
          </View>
    )
}
const styles = StyleSheet.create({
    card: {
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        marginTop: 10,
        height: 55
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        flexDirection: "row",
        justifyContent: "space-around",
        padding: 10
    },
})