import React, { useEffect, useState } from "react"
import { SafeAreaView, ScrollView, StyleSheet, Text, View, TextInput } from 'react-native'
import EncryptedStorage from 'react-native-encrypted-storage'
import axios from "axios";
import { Card, Button } from "react-native-paper";
import EmptyCart from "../components/EmptyCart";

export default function PengisianKomplainScreen ({navigation, route}){
    const { id_penjualan } = route.params;
    const { subject } = route.params;
    const [loading, setLoading] = useState(true);
    const [profile, setProfile] = useState([]);
    const [deskripsi, setDeskripsi] = useState([]);
    const [link, setLink] = useState([]);
    async function KirimKomplain(){
        if(id_penjualan == '' || subject == '' || deskripsi == '' || profile == ''){
            alert('Data Harus Diisi');
        }
        else{
            const data = {
                id_penjualan: id_penjualan[0],
                masalah: subject,
                deskripsi_masalah: deskripsi,
                email: profile,
                link_youtube: link
            }
            console.log(data)
            try{
                axios.post('http://192.168.43.22:8000/api/user/tambah_komplain', data).then(response => {
                    navigation.navigate('ProfileScreen')
                })
                .finally(() => setLoading(false));
            }
            catch(err){
                console.log(err);
            }
        }
        
    }
    useEffect(() => {
        async function getPesanan(){
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
                        const email = response.data.profile.email;
                        setProfile(response.data.profile.email);
                    })
                    .finally(() => setLoading(false));
                }
                catch(err){
                    console.log(err);
                }
            }
        }
        getPesanan()
    },[]);
    
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    return(
        <View style={{flex: 1, marginVertical: 10}}>
            <Card style={styles.card}>
                <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>{id_penjualan}</Text>
            </Card>
            <Card style={styles.card}>
                <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>{subject}</Text>
            </Card>
            <Card style={styles.card}>
                <TextInput placeholder="Deskripsikan Masalah Yang Anda Alami" 
                    value={deskripsi}
                    multiline={true}
                    numberOfLines={4}
                    onChangeText={(text) => setDeskripsi(text)}
                    style={{color: 'black'}}
                    placeholderTextColor="#000" 
                />
            </Card>
            <Card style={styles.card_warning}>
                <Text style={{marginLeft: 15, fontSize: 15, marginTop: 10, color: 'black'}}>Perhatian :</Text>
                <Text style={{marginLeft: 15, fontSize: 15, marginVertical: 5,marginBottom: 10, color: 'black'}}>Upload Bukti Video Unboxing Di Youtube, Kemudian Cantumkan Link Video Pada Form Dibawah ini.</Text>
            </Card>
            <Card style={styles.card}>
                <TextInput placeholder="Link Video Youtube" 
                    value={link}
                    onChangeText={(text) => setLink(text)}
                    style={{color: 'black'}}
                    placeholderTextColor="#000" 
                />
            </Card>
            <View style={{marginHorizontal: 10, marginTop: 10}}>
                <Button mode="contained" onPress={() => KirimKomplain()}>Kirim Komplain</Button>
            </View>
        </View>
    )
}
const styles = StyleSheet.create({
    button:{
        width: 50,
        height: 50,
        borderRadius: 50,
        backgroundColor: 'grey',
        marginTop: 15,
        marginLeft: 5,
        marginRight: 10,
    },
    card:{
        marginVertical: 5,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 5,
        borderWidth: 1,
    },
    card_warning:{
        marginVertical: 5,
        marginHorizontal: 10,
        backgroundColor: 'yellow',
        borderRadius: 5,
        borderWidth: 1,
    },
    card_deskripsi:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 90,
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 10
    },
    image:{
        width: 80,
        height: 80,
    },
    container: {
        backgroundColor: 'white',
        marginTop: 10,
        height: 200,
    },
    child: { 
        justifyContent: 'center' ,
    },
    text: { 
        fontSize: 50,
        textAlign: 'center' 
    },
    judul_barang: {
        marginVertical: 3,
        marginHorizontal: 3,
        color: "black",
    },
    appbar_total:{
        bottom: 0,
        marginHorizontal: 10,
        borderRadius: 25,
        height: 50,
        backgroundColor: 'white'
        
    },
    bottom_appbar: {
        position: 'fixed',
        left: 0,
        backgroundColor: 'transparent',
        right: 0,
        bottom: 0,
    },
    bottom_button: {
        marginHorizontal: 10
    }
});