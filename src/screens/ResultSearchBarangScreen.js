import React, { useEffect, useState } from 'react';
import { Text, StyleSheet, View, Image, TextInput, Dimensions, FlatList, Alert, ScrollView, SafeAreaView, TouchableOpacity } from 'react-native';
import { Button, Card, Appbar } from 'react-native-paper';
import axios from 'axios';
import EncryptedStorage from 'react-native-encrypted-storage';
import { SwiperFlatList } from 'react-native-swiper-flatlist';
import Icon from 'react-native-vector-icons/Feather';
import Logo from 'react-native-vector-icons/Ionicons';


export default function ResultSearchBarangScreen({ navigation, route }) {
    const { nama_barang } = route.params;
    const [data, setData] = useState([false]);
    
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    
    const renderItem = ({item}) => (   
        <View style={{flex: 1, marginHorizontal: 5, marginVertical: 5}}>
            <TouchableOpacity onPress={() => {
                navigation.navigate('DetailBarangScreen', {
                    id_barang: (item.id_barang),
                })
            }}>
                <Card style={styles.card}>
                    <Image source={{ uri: 'http://192.168.43.22:8000/'+item.gambar,}} style={styles.image}/>
                    <Text style={styles.judul_barang}>{item.nama_barang}</Text>
                    <Text style={styles.judul_barang}>{numberFormat(item.harga)}</Text>  
                </Card>
            </TouchableOpacity>
        </View>
    )
    useEffect(() => {
        async function getBarang(){
        let headers = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }
        axios.get(`http://192.168.43.22:8000/api/user/cari_barang?nama_barang=${nama_barang}`, headers).then(response => {
        console.log(response.data);
        setData(response.data);
            });
        }
        getBarang()
    },[]);
            return(
                <View style={{flex: 1}}>
                    <ScrollView showsHorizontalScrollIndicator={false}>
                        <View>
                            <View style={{ flexDirection: 'row' }}>
                                <View style={{ flex: 9 }}>
                                    <TextInput
                                        placeholder='Search'
                                        underlineColorAndroid='transparent'
                                        pointerEvents="none"
                                        onPressIn={() => {navigation.navigate('SearchBarangScreen')}}
                                        style={styles.form}
                                        value={nama_barang}
                                        theme={{ roundness: 8 }} />
                                </View>
                            </View>
                            <View style={{marginHorizontal: 10, marginVertical: 5}}>
                                <Text style={{fontSize: 15, color: 'black', marginHorizontal: 10}}>Menampilkan hasil dari {nama_barang}</Text>
                            </View>
                            {
                                nama_barang == "" ? 
                                <View></View> 
                                :
                                <View style={{marginHorizontal: 10, marginTop: 10}}>
                                    <FlatList scrollEnabled={false}
                                    keyExtractor={(item) => item.id_barang}
                                    numColumns={2}
                                    data={data}
                                    renderItem={renderItem} />
                                </View>
                            }
                        </View>
                    </ScrollView>
                </View>
            )
}

const { width } = Dimensions.get('window');
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
        backgroundColor: 'white',
        borderRadius: 5,
        borderWidth: 1,
        height: 260,
    },
    card_opsi:{
        backgroundColor: 'white',
        borderRadius: 5,
        borderWidth: 1,
    },
    image:{
        flex: 1,
        resizeMode: 'contain',
    },
    image_banner:{
        width: width,
        height: 150,
    },
    form:{
        marginTop: 15,
        borderColor: 'black',
        borderRadius: 5,
        height: 40,
        borderWidth: 1,
        marginHorizontal: 13,
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
        width,
        justifyContent: 'center' ,
    },
    text: { 
        fontSize: 50,
        textAlign: 'center' 
    },
    judul_barang: {
        marginVertical: 3,
        marginHorizontal: 10,
        fontSize: 15,
        color: "black"
    },
    bottom_appbar: {
        left: 0,
        backgroundColor: 'transparent',
        right: 0,
        bottom: 0,
    },
    bottom_button: {
        marginHorizontal: 10
    }
})

  