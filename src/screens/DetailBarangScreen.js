import React, { useEffect, useState } from 'react'
import { Text, Image, SafeAreaView, FlatList, View, StyleSheet, ScrollView, Alert, ToastAndroid, ActivityIndicator } from 'react-native';
import { Card, Appbar, Button } from 'react-native-paper'
import axios from 'axios';
import EncryptedStorage from 'react-native-encrypted-storage';
import { TouchableOpacity } from 'react-native-gesture-handler';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';

export default function DetailBarangScreen({ navigation, route }){
    const { id_barang } = route.params;
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        async function getDetailBarang(){
            let headers = {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }
            axios.get('http://192.168.43.22:8000/api/user/detail_barang',{params: {id_barang: id_barang}}, headers).then(response => {
                console.log(response.data);
                setData(response.data);
            })
            .finally(() => setLoading(false));
        }
        getDetailBarang()
    },[]);

    async function beliSekarang(){
        if(data.map(item => item.stok_barang) <= 0 || data.map(item => item.harga) <= 0){
            ToastAndroid.show("Stok Barang Tidak Mencukupi", ToastAndroid.SHORT);
        }
        else{
            navigation.navigate('BeliSekarangScreen', {
                id_barang: id_barang,
            })
        }
    }
    async function tambahKeranjang(){
        if(data.map(item => item.stok_barang) <= 0 || data.map(item => item.harga) <= 0){
            Alert.alert('Stok Barang Tidak Mencukupi');
        }
        else{
            const token = await EncryptedStorage.getItem("token");
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
                    console.log(email);
                    if(email == null){
                        navigation.reset({
                            index: 0,
                            routes: [{ name: 'LoginScreen' }],
                        })
                    }
                    else{
                        let headers = {
                            headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            }
                        }
                        let data = {
                            id_barang : id_barang,
                            email : email,
                            jumlah_barang : 1,
                        }
                        axios.post('http://192.168.43.22:8000/api/user/tambah_keranjang', data, headers).then(response => {
                            console.log(response.data.success);
                            if(response.data.success){
                                navigation.push('KeranjangScreen');
                            }
                            if(response.data.error){
                                alert(response.data.error);
                            }
                        });
                    }
                })
            }
            catch(err){
                console.log(err);
            }
        }
    }
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);

    const renderItem = ({item}) => (
        <View style={{flex : 1}}>
            <View style={{marginBottom: 40, marginVertical: 20, flex: 1}}>
                <Image source={{ uri: 'http://192.168.43.22:8000/' + item.gambar,}} style={styles.gambar}/>
            </View>
        </View>
    )
    const renderDetail = ({item}) => (
        <View style={{flex : 1}}>
            <View style={{marginVertical: 10, marginHorizontal: 10}}>
                <Text style={styles.judul}>{item.nama_barang} {item.ram}GB/{item.internal}GB - {item.warna}</Text>
            </View>
            <View style={{flexDirection: 'row', marginTop: -5}}>
                <View style={{flex: 1, marginHorizontal: 10}}>
                <Text style={{marginHorizontal: 5, marginVertical: 5, color: "black", fontSize: 20}}>{numberFormat(item.harga)}</Text>   
                </View>
                <View style={{flex: 1, marginHorizontal: 10, justifyContent: 'center'}}>
                    <Text style={{textAlign: 'center', marginHorizontal: 5, marginVertical: 5, color: "black", fontSize: 15}}>Terjual {item.terjual}</Text>
                </View>
            </View>
            <View>
                <Text style={{color: 'black', marginHorizontal: 10, marginTop: 20, marginBottom: 10,  fontSize: 20}}>Spesifikasi {data.map(item => item.nama_barang)}</Text>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Nama Barang</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.nama_barang}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Ram</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.ram} GB</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Internal</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.internal} GB</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Warna</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.warna}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Kamera Depan</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.kamera_depan}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Kamera Belakang</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.kamera_belakang}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Layar</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.layar}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Chipset(CPU)</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.chipset}</Text>
                </View>
            </View>
            <View style={{flexDirection: 'row'}}>
                <View style={{flex: 1}}>
                    <Text style={styles.judul}>Baterai</Text>
                </View>
                <View style={{flex: 2}}>
                    <Text style={styles.judul}>{item.baterai}</Text>
                </View>
            </View>
        </View>
    )
    return(
        <View style={{flex: 1}}>
            {loading == true ?
                <View style={styles.loading}>
                    <ActivityIndicator />
                </View>
                :
                <View style={{flex: 1}}>
                    <ScrollView>
                        <Card style={styles.card}>
                            <SafeAreaView style={{flex: 1}}>
                                <FlatList scrollEnabled={false}
                                keyExtractor={(item) => item.id_barang}
                                numColumns={2}
                                data={data}
                                renderItem={renderItem} />
                            </SafeAreaView>
                        </Card>
                        <View style={{marginTop: -25}}>
                            <Card style={styles.card_detail}>
                                <FlatList scrollEnabled={false}
                                keyExtractor={(item) => item.id_barang}
                                numColumns={2}
                                data={data}
                                renderItem={renderDetail} />
                            </Card>
                        </View>
                    </ScrollView>
                    <Appbar style={styles.bottom}>
                        <View style={{flexDirection: 'row'}}>
                            <View style={{flex: 1}}>
                                <Button icon="cart-arrow-down" style={styles.button} onPress={tambahKeranjang} mode="contained">
                                    Keranjang
                                </Button>
                            </View>
                            <View style={{flex: 1}}>
                                <Button mode="contained" style={styles.button}  onPress={beliSekarang}>Beli Sekarang</Button>
                            </View>
                        </View>
                    </Appbar>
                </View>
            }
        </View>
    )
}
const styles = StyleSheet.create({
    gambar: {
        flex: 1,
        aspectRatio: 1.5, 
        resizeMode: 'contain',
    },
    cardGambar: {
        backgroundColor: 'white',
        borderWidth: 2,
    },
    card: {
        backgroundColor: 'white',
        borderWidth: 2,
        flex: 1
    },
    card_detail: {
        backgroundColor: 'white',
        borderWidth: 2,
        borderTopRightRadius: 25,
        borderTopLeftRadius: 25,
        flex: 1,
        marginBottom:50
    },
    judul: {
        marginHorizontal: 5,
        marginVertical: 5,
        color: "black"
    },
    bottom: {
        position: 'absolute',
        flex: 1,
        left: 0,
        backgroundColor: 'transparent',
        right: 0,
        bottom: -2,
    },
    color: {
        height: '100%',
        width: '100%',
        opacity: 0.8,
        position: 'absolute',
        elevation: (Platform.OS === 'android') ? 50 : 0,
        backgroundColor: 'white',
        zIndex: 100,
        flex: 1
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        flexDirection: "row",
        justifyContent: "space-around",
        padding: 10
    },
    button: {
        marginHorizontal: 10,
        borderRadius: 25,
    },
})