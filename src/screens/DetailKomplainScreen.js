import React, { useEffect, useState } from "react"
import { SafeAreaView, ScrollView, StyleSheet, Text, View, TextInput } from 'react-native'
import EncryptedStorage from 'react-native-encrypted-storage'
import axios from "axios";
import { Card, Button } from "react-native-paper";
import EmptyCart from "../components/EmptyCart";

export default function DetailKomplainScreen ({navigation, route}){
    const { id_penjualan } = route.params;
    const [loading, setLoading] = useState(true);
    const [profile, setProfile] = useState([]);
    const [idKomplain, setIdKomplain] = useState([]);
    const [idPenjualan, setIdPenjualan] = useState([]);
    const [masalah, setMasalah] = useState([]);
    const [deskripsiMasalah, setDeskripsiMasalah] = useState([]);
    const [linkYoutube, setLinkYoutube] = useState([]);
    const [status, setStatus] = useState([]);
    const [tanggalKomplain, setTanggalKomplain] = useState([]);
    const [tanggapanAdmin, setTanggapanAdmin] = useState([]);

    async function komplainSelesai(){
        axios.get(`http://192.168.43.22:8000/api/user/komplain_selesai?id_penjualan=${id_penjualan}`).then(response => {
            console.log(response.data.success)
            if(response.data.success){
                navigation.goBack();
            }
            if(response.data.error){
                alert('Gagal');
            }
        })
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
                        axios.get(`http://192.168.43.22:8000/api/user/komplain?id_penjualan=${id_penjualan}`, headers).then(response => {
                            console.log(response.data);
                            setIdKomplain(response.data.id_komplain);
                            setIdPenjualan(response.data.id_penjualan);
                            setMasalah(response.data.masalah);
                            setDeskripsiMasalah(response.data.deskripsi_masalah);
                            setLinkYoutube(response.data.link_youtube);
                            setStatus(response.data.status);
                            setTanggalKomplain(response.data.tanggal_komplain);
                            setTanggapanAdmin(response.data.tanggapan_admin);
                        }) 
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
                <View style={{flexDirection: 'row'}}>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>{status}</Text>
                    </View>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black', textAlign: 'right'}}>{tanggalKomplain}</Text>
                    </View> 
                </View>
            </Card>
            <Card style={styles.card}>
                <View style={{flexDirection: 'row'}}>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginTop: 15, color: 'black'}}>ID Komplain</Text>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>ID Penjualan</Text>
                    </View>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginTop: 15, color: 'black', textAlign: 'right'}}>{idKomplain}</Text>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black', textAlign: 'right'}}>{idPenjualan}</Text>
                    </View> 
                </View>
            </Card>
            <Card style={styles.card}>
                <View style={{flexDirection: 'row'}}>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>Keluhan</Text>
                    </View>
                    <View style={{flex: 1}}>
                        <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black', textAlign: 'right'}}>{masalah}</Text>
                    </View> 
                </View>
            </Card>
            <Text style={{marginLeft: 15, marginTop: 10,fontSize: 18, color: 'black', fontWeight: 'bold'}}>Deskripsi Masalah</Text>
            <Card style={styles.card}>
                <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>{deskripsiMasalah}</Text>
            </Card>
            <Card style={styles.card}>
                <Text style={{marginHorizontal: 5, marginVertical: 15, color: 'black'}}>{linkYoutube}</Text>
            </Card>
            <Text style={{marginLeft: 15, marginTop: 10,fontSize: 18, color: 'black', fontWeight: 'bold'}}>Tanggapan Admin</Text>
            <Card style={styles.card}>
                <Text style={{marginHorizontal: 5, marginVertical: 5, color: 'black'}}>{tanggapanAdmin}</Text>
            </Card>
            <View style={{marginHorizontal: 10, marginTop: 10}}>
                <Button mode="contained" onPress={() => komplainSelesai()}>Selesaikan Komplain</Button>
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