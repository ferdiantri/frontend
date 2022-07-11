import React, { useEffect, useState } from 'react'
import { Text, Image, SafeAreaView, FlatList, View, StyleSheet, RefreshControl,ScrollView, ActivityIndicator,TouchableOpacity, TextInput, Alert, ToastAndroid } from 'react-native';
import { Card, Appbar, Button } from 'react-native-paper'
import axios from 'axios';
import EncryptedStorage from 'react-native-encrypted-storage';
import Icon from 'react-native-vector-icons/Feather';
import EmptyCart from '../components/EmptyCart';

export default function KeranjangScreen({ navigation }){
    const [data, setData] = useState([]);
    const [email, setEmail] = useState();
    const [empty, setEmpty] = useState(false);
    const [loading, setLoading] = useState(true);

    const renderItem = ({item}) => (   
        <View style={{marginTop: 15, flex: 1}}>
            <TouchableOpacity onPress={() => {
                navigation.navigate('DetailBarangScreen', {
                    id_barang: (item.id_barang),
                })
            }}>
                <Card style={styles.card}>
                        <View style={{ flexDirection: 'row' }}>
                            <View style={{flex: 2}}>
                                <Image source={{ uri: 'http://192.168.43.22:8000/' + item.gambar }} style={styles.image} />
                            </View>
                            <View style={{ flexDirection: 'column', flex: 3, marginHorizontal: 5, marginVertical: 5 }}>
                                <View>
                                    <Text style={styles.judul_barang}>{item.nama_barang}</Text>
                                </View>
                                <View>
                                    <Text style={styles.judul_barang}>{item.ram}GB/{item.internal}GB - {item.warna}</Text>
                                </View>
                                <View>
                                    <Text style={styles.judul_barang}>{numberFormat(item.harga)}</Text>
                                </View>
                                
                                <View style={{ flexDirection: 'row', marginVertical: 15}}>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <TouchableOpacity onPress={() => kurangKeranjang(item.id_barang, item.jumlah_barang)}>
                                            <Icon name='minus' size={30} color='black' />
                                        </TouchableOpacity>
                                    </View>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <Text style={{fontSize: 20, color: 'black'}}>{item.jumlah_barang}</Text>
                                    </View>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <TouchableOpacity onPress={() => tambahKeranjang(item.id_barang, item.jumlah_barang)}>
                                            <Icon name='plus' size={30} color='black'/>
                                        </TouchableOpacity>
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

        async function onRefresh(){
            setLoading(true);
            getKeranjang();
        }

        async function getKeranjang(){
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
                            console.log(email);
                            setEmail(email);
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
                                axios.get(`http://192.168.43.22:8000/api/user/keranjang?email=${response.data.profile.email}`, headers).then(response => {
                                    if(response.data.error){
                                        setEmpty(true);
                                        console.log(response.data.error);
                                    }
                                    else{
                                        setEmpty(false);
                                        console.log(response.data);
                                        setData(response.data);
                                    }
                                })
                                .finally(() => setLoading(false));
                            }
                        })
                    }
                    catch(err){
                        console.log(err);
                    }
                }
            
        }

        useEffect(() => {
            const unsubscribe = navigation.addListener('focus', () => {
                getKeranjang();
            });
            unsubscribe
            getKeranjang()
        },[]);

        async function tambahKeranjang(id_barang){
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
              axios.post('http://192.168.43.22:8000/api/user/tambah_keranjang', data, headers).then(async(response) => {
                console.log(response.data);
                if(response.data.success){
                    axios.get(`http://192.168.43.22:8000/api/user/keranjang?email=${email}`, headers).then(async(response) => {
                            console.log(response.data);
                            setData(response.data);
                         });
                }
            });
        }
        async function kurangKeranjang(id_barang, jumlah_barang){
            if(jumlah_barang <= 1){
                let headers = {
                    headers: {
                      'Accept': 'application/json',
                      'Content-Type': 'application/json',
                    }
                   }
                let data = {
                    id_barang : id_barang,
                    email : email,
                  }
                axios.post('http://192.168.43.22:8000/api/user/delete_keranjang', data, headers).then(async(response) => {
                    console.log(response.data);
                    getKeranjang();
                });
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
                    jumlah_barang : -1,
                  }
                  axios.post('http://192.168.43.22:8000/api/user/tambah_keranjang', data, headers).then(async(response) => {
                    console.log(response.data);
                    getKeranjang();
                });
            }
        }
        return(
            <View style={{flex: 1}}>
                {
                    loading == true ? 
                    <View style={styles.loading}>
                        <ActivityIndicator />
                    </View>
                    :
                    <View style={{flex: 1}}>
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
                            <View style={{flex: 1}}>
                                <ScrollView refreshControl={
                                    <RefreshControl
                                        refreshing={loading}
                                        onRefresh={onRefresh}
                                    />
                                }>
                                    <View style={{flex: 1, marginBottom: 75 }}>
                                        <SafeAreaView>
                                            <FlatList scrollEnabled={false}
                                                keyExtractor={(item) => item.id_barang}
                                                numColumns={1}
                                                data={data}
                                                renderItem={renderItem} />
                                        </SafeAreaView>
                                    </View>
                                </ScrollView>
                                <View style={{ bottom: 60 }}>
                                    <Appbar style={styles.appbar_total}>
                                        <View style={{ flex: 1 }}>
                                            <Text style={{ fontSize: 15, marginVertical: 15, textAlign: 'right', color: 'black' }}>{numberFormat(data.map(item => item.jumlah_barang * item.harga).reduce((x, y) => x + y, 0))} </Text>
                                        </View>
                                        <View>
                                            <Button onPress={() => navigation.navigate('PembelianScreen')}><Text style={{ color: 'black' }}>CheckOut</Text></Button>
                                        </View>
                                    </Appbar>
                                </View>
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
        height: 160,
    },
    image:{
        width: 150,
        height: 150,
    },
    form:{
        marginTop: 15,
        borderColor: 'black',
        borderRadius: 25,
        height: 50,
        borderWidth: 1,
        marginLeft: 10,
        marginRight: 10,
        padding: 3,
        fontSize: 18,
        color: 'black',
        backgroundColor: 'white'
    },
    container: {
        backgroundColor: 'white',
        marginTop: 10,
        height: 200,
    },
    child: { 
        justifyContent: 'center' ,
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        flexDirection: "row",
        justifyContent: "space-around",
        padding: 10
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
        borderRadius: 15,
        height: 50,
        backgroundColor: 'white'
        
    },
    bottom_appbar: {
        position: 'absolute',
        left: 0,
        backgroundColor: 'transparent',
        right: 0,
        bottom: 0,
    },
    bottom_button: {
        marginHorizontal: 10
    }
})
