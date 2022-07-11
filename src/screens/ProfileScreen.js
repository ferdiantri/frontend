import React, { useEffect, useState } from "react"
import { SafeAreaView, ScrollView, StyleSheet, Text, View, ActivityIndicator, RefreshControl, ImageBackground, TouchableOpacity, Image, ToastAndroid } from 'react-native'
import EncryptedStorage from 'react-native-encrypted-storage'
import axios from "axios";
import { Card, Appbar, Button } from "react-native-paper";
import { Badge } from 'react-native-paper';
import Material from "react-native-vector-icons/FontAwesome5";
import Ant from "react-native-vector-icons/AntDesign";
import {Shapes} from "react-native-background-shapes";

export default function ProfileScreen({ navigation }){
    const [data, setData] = useState([]);
    const [badge, setBadge] = useState({pending: '', settled: '', processed: '', delivery: '', delivered: '', complaint: ''});
    const [email, setEmail] = useState();
    const [loading, setLoading] = useState(true);
    const [refreshing, setrefreshing] = useState(false);

    // const onRefresh = React.useCallback(() => {
    async function onRefresh(){
        setrefreshing(true);
        try{  
            let headers = {
                headers: {
                  'Accept': 'appl/ication/json',
                  'Content-Type': 'application/json',
                }
            }
            const token_email = await EncryptedStorage.getItem("email");
            axios.get(`http://192.168.43.22:8000/api/user/refresh?email=${token_email}`, headers).then(response => {
                getProfile();
                console.log(response.data.xendit)
            })
            .finally(() => setrefreshing(false));
        }
        catch(err){
            console.log(err);
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
            try{
                axios.get('http://192.168.43.22:8000/api/user/profile', headers).then(response => {
                    setData(response.data.profile);
                    setEmail(response.data.profile.email);
                    axios.get(`http://192.168.43.22:8000/api/user/badge_penjualan?email=${response.data.profile.email}`, headers).then(response => {
                        console.log(response.data)
                        if(response.data.error){
                            setBadge({pending: 0, settled: 0, processed: 0, delivery: 0, delivered: 0})
                        }
                        if(response.data.barang){
                            let pending = 0;
                            let settled = 0;
                            let processed = 0;
                            let delivery = 0;
                            let delivered = 0;
                            let complaint = 0;
                            response.data.barang.map(item => {
                                if (item.status === "PENDING") {
                                    pending++;
                                }
                            });
                            response.data.barang.map(item => {
                                if (item.status === "SETTLED") {
                                    settled++;
                                }
                            });
                            response.data.barang.map(item => {
                                if (item.status === "PROCESSED") {
                                    processed++;
                                }
                            });
                            response.data.barang.map(item => {
                                if (item.status === "DELIVERY") {
                                    delivery++;
                                }
                            });
                            response.data.barang.map(item => {
                                if (item.status === "DELIVERED") {
                                    delivered++;
                                }
                            });
                            response.data.barang.map(item => {
                                if (item.status === "COMPLAINT") {
                                    complaint++;
                                }
                            });
                            setBadge({pending: pending, settled: settled, processed: processed, delivery: delivery, delivered: delivered, complaint: complaint})

                        }
                        
                    })
                })
            }
            catch(err){
                console.log(err);
            }
        }
    }
    async function onLogoutPressed(){
        const token = await EncryptedStorage.getItem("token");
        let headers = {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer '+token
          }
         }
        axios.get('http://192.168.43.22:8000/api/user/logout', headers).then(async(response) => {
          console.log(response.data);
          
          if(response.data.success){
            await EncryptedStorage.removeItem("token");
            await EncryptedStorage.removeItem("email");
            navigation.reset({
              index: 0,
              routes: [{ name: 'LoginScreen' }],
            })
          }
          });
      }

    useEffect(() => {        
        const unsubscribe = navigation.addListener('focus', () => {
            getProfile()
        });
        unsubscribe
        getProfile().finally(() => setLoading(false));
        },[]);
    return(
        <View style={{flex: 1}}>
            {
                loading == true ?
                <View style={styles.loading}>
                    <ActivityIndicator />
                </View>
                :
                <View style={{flex: 1}}>
                {/* <ImageBackground source={require('../assets/background.png')} resizeMode="cover" style={{flex: 1}}> */}
                    <Shapes
                        primaryColor="#00aced"
                        secondaryColor="#2F53D5"
                        height={3}
                        borderRadius={20}
                        style={{height: 220}}
                        figures={[
                        {name: 'circle', position: 'center', size: 100},
                        {name: 'circle', position: 'flex-start', axis: 'top', size: 100},
                        {name: 'circle', position: 'center', axis: 'right', size: 100},
                        ]}
                    />
                    <SafeAreaView>
                        <ScrollView refreshControl={
                            <RefreshControl
                                refreshing={refreshing}
                                onRefresh={onRefresh}
                            />
                            }>
                            <View>
                                {/* <Card style={styles.card}> */}
                                <View style={{marginHorizontal: 15, marginVertical: 15}}>
                                    <View style={{ flexDirection: 'row' }}>
                                        <View style={styles.profile_photo}>
                                            <TouchableOpacity onPress={() => {navigation.navigate('UploadScreen', {profile_picture: (data.profile_picture)})}}>
                                                <Image source={{ uri: 'http://192.168.43.22:8000/' + data.profile_picture }} style={{width: 75, height: 75, borderRadius: 50,}} />
                                            </TouchableOpacity>
                                        </View>
                                        <View style={styles.profile_data}>
                                            <Text style={{fontSize: 20, color : 'black', marginHorizontal: 20}}>{data.nama}</Text>
                                            <Text style={styles.text}>{data.email}</Text>
                                            <Text style={styles.text}>{data.nomor_telepon}</Text>
                                        </View>
                                        <View style={{marginHorizontal: 15, marginVertical: 30}}>
                                            <TouchableOpacity onPress={() => onLogoutPressed()}>
                                                <Ant name='logout' size={30} color='black'/>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </View>
                                {/* </Card> */}
                                {/* <Text style={{marginHorizontal: 10, fontSize: 18, color: 'black'}}>Daftar Pesanan</Text> */}
                                <Card style={styles.card_pesanan_all}>
                                    <View>
                                        <View style={styles.content_semua_pesanan}> 
                                            <TouchableOpacity onPress={() => navigation.navigate('SemuaPesananScreen')}>
                                                <Text style={{fontSize: 15, color: 'black'}}>Semua Pesanan</Text>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </Card>
                                <Card style={styles.card_pesanan}>
                                    <View style={{ flexDirection: 'row' }}>
                                        <View style={styles.content_pesanan}>
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananBelumDibayarScreen')}>
                                                {
                                                    badge.pending == 0 ?
                                                    <View style={{marginTop: 20}}>
                                                        
                                                    </View>
                                                    :
                                                    <View>
                                                        <Badge>{badge.pending}</Badge>
                                                    </View>
                                                }
                                                <Material name='wallet' size={30} color='black'/>
                                                <Text style={{fontSize: 12, color: 'black'}}>Belum Dibayar</Text>
                                            </TouchableOpacity>
                                        </View>
                                        <View style={styles.content_pesanan}>
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananDibayarScreen')}>
                                            {
                                                    badge.settled == 0 ?
                                                    <View style={{marginTop: 20}}>
                                                        
                                                    </View>
                                                    :
                                                    <View>
                                                        <Badge>{badge.settled}</Badge>
                                                    </View>
                                                }
                                                <Material name='money-check' size={30} color='black'/>
                                                <Text style={{fontSize: 12, color: 'black'}}>Sudah Dibayar</Text>
                                            </TouchableOpacity>
                                        </View>
                                        <View style={styles.content_pesanan}>
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananDalamProsesScreen')}> 
                                            {
                                                    badge.processed == 0 ?
                                                    <View style={{marginTop: 20}}>
                                                        
                                                    </View>
                                                    :
                                                    <View>
                                                        <Badge>{badge.processed}</Badge>
                                                    </View>
                                                }
                                                <Material name='box' size={30} color='black'/>
                                                <Text style={{fontSize: 12, color: 'black'}}>Dalam Proses</Text>
                                            </TouchableOpacity>
                                        </View>
                                        <View style={styles.content_pesanan}>
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananSedangDikirimScreen')}> 
                                            {
                                                    badge.delivery == 0 ?
                                                    <View style={{marginTop: 20}}>
                                                        
                                                    </View>
                                                    :
                                                    <View>
                                                        <Badge>{badge.delivery}</Badge>
                                                    </View>
                                                }
                                                <Material name='shipping-fast' size={30} color='black'/>
                                                <Text style={{fontSize: 12, color: 'black'}}>Sedang Dikirim</Text>
                                            </TouchableOpacity>
                                        </View> 
                                        <View style={styles.content_pesanan}>
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananTerkirimScreen')}>
                                            {
                                                badge.delivered == 0 ?
                                                <View style={{marginTop: 20}}>
                                                    
                                                </View>
                                                :
                                                <View>
                                                    <Badge>{badge.delivered}</Badge>
                                                </View>
                                                } 
                                                <Material name='clipboard-check' size={30} color='black'/>
                                                <Text style={{fontSize: 12, color: 'black'}}>Selesai</Text>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </Card>
                                <Card style={styles.card_pesanan_komplain}>
                                    <View>
                                        <View style={styles.content_semua_pesanan}> 
                                            <TouchableOpacity onPress={() => navigation.navigate('PesananDikomplainScreen')}>
                                                <View style={{flexDirection: 'row'}}>
                                                    <View style={{flex: 1}}>
                                                        <Text style={{fontSize: 15, color: 'black'}}>Pesanan Dikomplain</Text>
                                                    </View>
                                                    <View style={{flex: 1}}>
                                                        {
                                                            badge.complaint == 0 ?
                                                            <View style={{marginTop: 20}}>
                                                                
                                                            </View>
                                                            :
                                                            <View style={{alignItems: 'flex-end'}}>
                                                                <Badge>{badge.complaint}</Badge>
                                                            </View>
                                                        } 
                                                    </View>
                                                </View>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                </Card>
                            </View>
                        </ScrollView>
                    </SafeAreaView>
                {/* </ImageBackground> */}
                </View>
            }
        </View>
    )
}
const styles = StyleSheet.create({
    card : {
        marginTop : 15,
        marginHorizontal: 10,
        borderWidth: 1,
        backgroundColor: 'white',
    },
    content_semua_pesanan : {
        marginTop : 15,
        marginHorizontal: 10,
    },
    card_pesanan : {
        marginHorizontal: 10,
        borderWidth: 1,
        backgroundColor: 'white',
        justifyContent: 'center',
        alignContent: 'center',
        borderBottomRightRadius: 25,
        borderBottomLeftRadius: 25,
        flex: 1
    },
    profile_data : {
        flex: 4,
        marginVertical : 10
    },
    profile_photo : {
        flex: 1,
        marginHorizontal : 20,
        marginVertical : 10,
        marginHorizontal: 15
    },
    content_pesanan : {
        marginHorizontal : 10,
        marginVertical : 10,
        flex: 1,
        alignItems: 'center',
    },
    card_pesanan_all : {
        marginHorizontal: 10,
        borderWidth: 1,
        borderTopRightRadius: 25,
        borderTopLeftRadius: 25,
        backgroundColor: 'white',
        height: 50,
        flex: 1
    },
    card_pesanan_komplain : {
        marginHorizontal: 10,
        marginVertical: 10,
        borderWidth: 1,
        borderRadius: 25,
        backgroundColor: 'white',
        height: 50,
        flex: 1
    },
    text : {
        color : 'black',
        marginHorizontal: 20,
        padding: 2
    },
    bottom_appbar: {
        position: 'absolute',
        left: 0,
        backgroundColor: 'white',
        right: 0,
        bottom: 0,
    },
    bottom_button: {
        marginHorizontal: 10
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 10
    },
    badge:{
        color:'#fff',
        position:'absolute',
        zIndex:10,
        top:1,
        right:1,
        padding:1,
        backgroundColor:'red',
        borderRadius:5
      }
})