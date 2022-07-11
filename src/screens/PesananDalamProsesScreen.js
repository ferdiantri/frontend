import React, { useEffect, useState } from "react"
import { SafeAreaView, ScrollView, StyleSheet, Text, View, FlatList, ActivityIndicator, Button, TouchableOpacity, Image, Alert, RefreshControl } from 'react-native'
import EncryptedStorage from 'react-native-encrypted-storage'
import axios from "axios";
import { Card } from "react-native-paper";
import EmptyCart from "../components/EmptyCart";

export default function PesananBelumDibayarScreen ({navigation}){
    const [empty, setEmpty] = useState(false);
    const [profile, setProfile] = useState();
    const [barang, setBarang] = useState([]);
    const [loading, setLoading] = useState(true);
    async function onRefresh(){
        setLoading(true);
        try{  
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            console.log(profile)
            axios.get(`http://192.168.43.22:8000/api/user/refresh?email=${profile}`, headers).then(response => {
            console.log(response.data)
            })
            axios.get(`http://192.168.43.22:8000/api/user/penjualan_dalam_proses?email=${profile}`, headers).then(response => {
            console.log(response.data.barang)
                if(response.data.error){
                    setEmpty(true);
                }
                else{
                    setBarang(response.data.barang)
                }   
            })
            .finally(() => setLoading(false));
        }
        catch(err){
            console.log(err);
        }
      }

    useEffect(() => {
        async function getDaftarPesanan(){
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
                        axios.get(`http://192.168.43.22:8000/api/user/penjualan_dalam_proses?email=${email}`, headers).then(response => {
                            console.log(response.data.barang)
                            if(response.data.error){
                                setEmpty(true);
                            }
                            else{
                                setBarang(response.data.barang)
                            }
                        })
                    })
                    .finally(() => setLoading(false));
                }
                catch(err){
                    console.log(err);
                }
            }
        }
        getDaftarPesanan()
    },[]);
    const renderItem = ({item}) => (   
        <View style={{flex: 1}}>
            <TouchableOpacity onPress={() => navigation.navigate('DetailPesananScreen', {id_penjualan: (item.id_penjualan), from: (item.status)})}>
                <Card style={styles.card}>
                    <View style={{ flexDirection: 'row', borderBottomColor: 'black', borderBottomWidth: 1,}}>
                        <View style={{flex: 1}}>
                            <Text style={{textAlign: 'left', color: 'black', marginHorizontal: 10, marginVertical: 10}}>{item.status}</Text>
                        </View>
                        <View style={{flex: 1}}>
                            <Text style={{textAlign: 'right', color: 'black', marginHorizontal: 10, marginVertical: 10}}>{item.tanggal_penjualan}</Text>
                        </View>
                    </View>
                    <View style={{ flexDirection: 'row' }}>
                        <View style={{ flexDirection: 'column', flex: 4.5, marginHorizontal: 5, marginVertical: 5 }}>
                            {item.barang.map(data_barang => {
                                return(
                                    <View style={{ flexDirection: 'row' }}>
                                        <View style={{flex: 1}}>
                                            <Image source={{ uri: 'http://192.168.43.22:8000/' + data_barang.gambar }} style={styles.image} />
                                        </View>
                                        <View style={{flex: 3}}>
                                            <Text style={{color: 'black'}}>{data_barang.nama_barang}</Text>
                                            <Text style={{color: 'black'}}>x{data_barang.jumlah_barang}</Text>
                                        </View>
                                    </View>
                                )
                            })}
                                <View style={{marginHorizontal: 10, flexDirection: 'row'}}>
                                    <View style={{flex: 1}}>
                                        <Text style={{color: 'black'}}>Total Belanja</Text>
                                        <Text style={{color: 'black'}}>{numberFormat(item.total_harga)}</Text>
                                    </View>
                                    <View>
                                        <Button onPress={() => {navigation.navigate('DetailPesananScreen', {id_penjualan: (item.id_penjualan), from: (item.status)})}} title='Detail Pesanan'/>
                                    </View>
                                </View>
                            </View>
                    </View>     
                </Card>
            </TouchableOpacity>
        </View>
    )
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    return(
        <View style={{flex: 1}}>
            { 
                loading == true ?
                <View style={styles.loading}>
                    <ActivityIndicator />
                </View>
                :
                <View>
                    {empty ? 
                        <ScrollView refreshControl={
                            <RefreshControl
                                refreshing={loading}
                                onRefresh={onRefresh}
                            />
                        }>
                            <EmptyCart/>   
                        </ScrollView>
                    : 
                    <View>
                        <ScrollView refreshControl={
                            <RefreshControl
                                refreshing={loading}
                                onRefresh={onRefresh}
                            />
                        }>
                            <View style={{marginTop: 15}}>
                                <FlatList scrollEnabled={false}
                                    key={item => item.id_penjualan}
                                    numColumns={1}
                                    data={barang}
                                    renderItem={renderItem} />
                            </View>
                        </ScrollView>
                    </View>
                    }
                </View>
            }
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
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
    },
    card_total_harga:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 90,
    },
    card_alamat:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 80,
    },
    card_kurir:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 50,
    },
    card_voucher:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 40,
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