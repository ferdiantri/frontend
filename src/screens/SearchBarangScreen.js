import React, { useEffect, useState } from 'react';
import { Text, StyleSheet, View, Image, TextInput, Dimensions, FlatList, Alert, ScrollView, SafeAreaView, TouchableOpacity } from 'react-native';
import { Button, Card, Appbar } from 'react-native-paper';
import axios from 'axios';
import EncryptedStorage from 'react-native-encrypted-storage';
import { SwiperFlatList } from 'react-native-swiper-flatlist';
import Icon from 'react-native-vector-icons/Feather';
import Logo from 'react-native-vector-icons/Ionicons';


export default function SearchBarangScreen({ navigation }) {
    const [data, setData] = useState([false]);
    const [search, setSearch] = useState("");
    
    const renderItem = ({item}) => (   
    <View style={{flex: 1}}>
        <TouchableOpacity onPress={() => {navigation.navigate('ResultSearchBarangScreen', {nama_barang: (item.nama_barang)})
        }}>
            <Text style={styles.judul_barang}>{item.nama_barang} {item.warna}</Text>
        </TouchableOpacity>
    </View>
    )
    async function searchFromNameProduct(e){
        console.log(e);
        setSearch(e);
        if(!e){
            console.log("Tidak Boleh Kosong");
        }
        else{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            axios.get(`http://192.168.43.22:8000/api/user/cari_barang?nama_barang=${e}`, headers).then(response => {
                console.log(response.data);
                setData(response.data);
            })
        }
    }
    async function searchFromForm(e){
        if(!search){
            console.log("Tidak Boleh Kosong");
        }
        else{
            navigation.navigate('ResultSearchBarangScreen', {nama_barang: search})
        }
    }
            return(
                <View style={{flex: 1}}>
                    <ScrollView showsHorizontalScrollIndicator={false}>
                        <View>
                            <View style={{ flexDirection: 'row' }}>
                                <View style={{ flex: 9 }}>
                                    <TextInput
                                        placeholder='Search'
                                        underlineColorAndroid='transparent'
                                        style={styles.form}
                                        value={search}
                                        onChangeText={(e) => { setSearch(e); searchFromNameProduct(e); }}
                                        onSubmitEditing={searchFromForm}
                                        theme={{ roundness: 8 }} />
                                </View>
                            </View>
                            {
                                search == "" ? 
                                <View></View> 
                                :
                                <View style={{marginHorizontal: 10, marginTop: 10}}>
                                    <SafeAreaView style={{flex: 1}}>
                                        <Card style={styles.card_opsi}>
                                                <FlatList scrollEnabled={false}
                                                    keyExtractor={(item) => item.id_barang}
                                                    numColumns={1}
                                                    data={data}
                                                    renderItem={renderItem} />
                                        </Card>
                                    </SafeAreaView>
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
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
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
        marginHorizontal: 10,
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
        fontSize: 17,
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

  