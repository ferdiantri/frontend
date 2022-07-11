import React, { useEffect, useState } from 'react'
import { Text, Image, SafeAreaView, FlatList, View, StyleSheet, ScrollView, TouchableOpacity, ActivityIndicator } from 'react-native';
import { Card, Appbar, Button } from 'react-native-paper'
import axios from 'axios';


export default function DetailPesananScreen({ navigation, route }){
    const { id_penjualan } = route.params;
    const { from } = route.params;
    const [barang, setBarang] = useState([]);
    const [loading, setLoading] = useState(true);
    const [daftarBarang, setDaftarBarang] = useState([]);
    const [logPenjualan, setLogPenjualan] = useState([]);
    const [logPengiriman, setLogPengiriman] = useState([]);
    const [onDelivered, setOnDelivered] = useState([]);
    const headers = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    }

    async function setDelivered(){
        axios.get(`http://192.168.43.22:8000/api/user/proses_penjualan_terkirim?id_penjualan=${id_penjualan}`, headers).then(response => {
            console.log(response.data.barang)
            if(response.data.barang){
                navigation.navigate('PesananTerkirimScreen')
            }
        })
    }

    useEffect(() => {
        async function getDetailPesanan(){
            from == 'PENDING' ?
                axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_belum_dibayar?id_penjualan=${id_penjualan}`, headers).then(response => {
                    console.log(response.data.barang);
                    setBarang(response.data.barang);
                    setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                    setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                    console.log(daftarBarang);
                })
                .finally(() => setLoading(false))
                :
                from == 'SETTLED' ? 
                    axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_dibayar?id_penjualan=${id_penjualan}`, headers).then(response => {
                        console.log(response.data.barang);
                        setBarang(response.data.barang);
                        setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                        setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                        console.log(daftarBarang);
                    })
                    .finally(() => setLoading(false))
                    :
                    from == 'PROCESSED' ? 
                        axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_dalam_proses?id_penjualan=${id_penjualan}`, headers).then(response => {
                            console.log(response.data.barang);
                            setBarang(response.data.barang);
                            setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                            setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                            console.log(daftarBarang);
                        })
                        .finally(() => setLoading(false))
                        :
                        from == 'DELIVERY' ?
                            axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_sedang_dikirim?id_penjualan=${id_penjualan}`, headers).then(response => {
                                console.log(response.data.barang);
                                setBarang(response.data.barang);
                                setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                                setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                                console.log(barang);

                                const api = 'API KEY BINDERBYTE'
                                const courier = response.data.barang.map(item => item.jasa_pengiriman)[0];
                                const nomor_resi = response.data.barang.map(item => item.nomor_resi)[0];
                                axios.get(`https://api.binderbyte.com/v1/track?api_key=${api}&courier=${courier}&awb=${nomor_resi}`).then(response => {
                                    console.log(response.data.data.history);
                                    setLogPengiriman(response.data.data.history);
                                    setOnDelivered(response.data.data.summary.status);
                                }).finally(() => setLoading(false))
                            })
                            :
                            from == 'DELIVERED' ?
                                axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_terkirim?id_penjualan=${id_penjualan}`, headers).then(response => {
                                    console.log(response.data.barang);
                                    setBarang(response.data.barang);
                                    setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                                    setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                                    console.log(barang);
                                })
                                .finally(() => setLoading(false))
                                :
                                from == 'EXPIRED' ? 
                                    axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_tidak_terbayar?id_penjualan=${id_penjualan}`, headers).then(response => {
                                        console.log(response.data.barang);
                                        setBarang(response.data.barang);
                                        setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                                        setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                                        console.log(barang);
                                    })
                                    .finally(() => setLoading(false))
                                    :
                                    from == 'COMPLAINT' ? 
                                        axios.get(`http://192.168.43.22:8000/api/user/detail_penjualan_dikomplain?id_penjualan=${id_penjualan}`, headers).then(response => {
                                            console.log(response.data.barang);
                                            setBarang(response.data.barang);
                                            setDaftarBarang(response.data.barang.map(item => item.barang)[0]);
                                            setLogPenjualan(response.data.barang.map(item => item.log_penjualan)[0]);
                                            console.log(barang);
                                        })
                                        .finally(() => setLoading(false))
                                        :
                                        console.log('selesai')
                            
        }
        getDetailPesanan()
    },[]);
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    const renderLogPengiriman = ({item}) => (   
        <View style={{flex: 1, flexDirection: 'row', marginHorizontal: 10}}>
            <View style={{flex: 1}}>
                <Text style={{color: 'black', marginHorizontal: 3, marginVertical: 3, textAlign: 'left'}}>{item.desc}</Text>
            </View>
            <View style={{flex: 1}}>
                <Text style={{color: 'black', marginHorizontal: 3, marginVertical: 3, textAlign: 'right'}}>{item.date}</Text>
            </View>
        </View>
    )
    const renderLogPenjualan = ({item}) => (   
        <View style={{flex: 1, flexDirection: 'row', marginHorizontal: 10}}>
            <View style={{flex: 1}}>
                <Text style={{color: 'black', marginHorizontal: 3, marginVertical: 3, textAlign: 'left'}}>{item.status_log}</Text>
            </View>
            <View style={{flex: 1}}>
                <Text style={{color: 'black', marginHorizontal: 3, marginVertical: 3, textAlign: 'right'}}>{item.tanggal_penjualan_log}</Text>
            </View>
        </View>
    )
    const renderItem = ({item}) => (   
        <View style={{flex: 1}}>
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
                            <View style={{ flexDirection: 'column', flex: 4.5, marginHorizontal: 5, marginVertical: 5 }}>
                                <View>
                                    <Text style={styles.judul_barang}>{item.nama_barang}</Text>
                                </View>
                                <View>
                                    <Text style={styles.judul_barang}>{item.ram}GB/{item.internal}GB - {item.warna}</Text>
                                </View>
                                <View>
                                    <Text style={styles.judul_barang}>{numberFormat(item.harga_barang)}</Text>
                                </View>
                                <View>
                                    <Text style={styles.judul_barang}>x{item.jumlah_barang}</Text>
                                </View>
                            </View>
                        </View>     
                    </Card>
            </TouchableOpacity>
        </View>
    )
    return(
        <View style={{flex : 1}}>
            {
                loading == true ?
                <View style={styles.loading}>
                    <ActivityIndicator />
                </View>
                :
                <View style={{flex : 1}}>
                    <ScrollView>
                        <View>
                            <Card style={styles.card_status}>
                                <View style={{ flexDirection: 'row', borderBottomColor: 'black', borderBottomWidth: 1,}}>
                                    <View style={{flex: 1}}>
                                        <Text style={{textAlign: 'left', color: 'black', marginHorizontal: 10, marginVertical: 10}}>{barang.map(item => item.status)}</Text>
                                    </View>
                                    <View style={{flex: 1}}>
                                        <Text style={{textAlign: 'right', color: 'black', marginHorizontal: 10, marginVertical: 10}}>{barang.map(item => item.tanggal_penjualan)}</Text>
                                    </View>
                                </View>
                            </Card>
                        </View>
                        <View>
                            <Card style={styles.card_alamat}>
                                <TouchableOpacity onPress={() => navigation.navigate('AlamatScreen')}>
                                    <Text style={{ marginLeft: 10, color: 'black', marginTop: 10}}>{barang.map(item => item.nama_penerima)} ({barang.map(item => item.nomor_telepon)})</Text>
                                    <Text style={{ marginLeft: 10, color: 'black',}}>{barang.map(item => item.alamat)}</Text>
                                    <Text style={{ marginLeft: 10, color: 'black',}}>{barang.map(item => item.kabupaten)}, {barang.map(item => item.provinsi)}, {barang.map(item => item.kode_pos)}</Text>
                                </TouchableOpacity>
                            </Card>
                        </View>
                        <View>
                            <ScrollView>
                                <SafeAreaView style={{flex: 1}}>
                                    <FlatList scrollEnabled={false}
                                    keyExtractor={(item) => item.nama_barang}
                                    numColumns={1}
                                    data={daftarBarang}
                                    renderItem={renderItem} />
                                </SafeAreaView>
                            </ScrollView>
                        </View>
                        <View>
                            <Card style={styles.card_total_harga}>
                                <View style={{marginHorizontal: 10, marginVertical: 5}}>
                                <View style={{flexDirection: 'row'}}>
                                        <Text style={{flex: 1, color: "black",}}>ID Penjualan</Text>
                                        <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{barang.map(item => item.id_penjualan)}</Text>
                                    </View>
                                    <View style={{flexDirection: 'row'}}>
                                        <Text style={{flex: 1, color: "black",}}>Jasa Pengiriman</Text>
                                        <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{barang.map(item => item.jasa_pengiriman)}</Text>
                                    </View>
                                    <View style={{flexDirection: 'row'}}>
                                        <Text style={{flex: 1, color: "black",}}>Ongkos Kirim</Text>
                                        <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(barang.map(item => item.ongkir))}</Text>
                                    </View>
                                    <View style={{flexDirection: 'row'}}>
                                        <Text style={{flex: 1, color: "black",}}>Voucher</Text>
                                        <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(barang.map(item => item.potongan))} ({barang.map(item => item.kode_voucher.map(vc => vc.kode))})</Text>
                                    </View>
                                    <View style={{flexDirection: 'row'}}>
                                        <Text style={{flex: 1, color: "black",}}>Total Harga</Text>
                                        <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(barang.map(item => item.total_harga))}</Text>
                                    </View>
                                </View>
                            </Card>
                        </View>
                        <View>
                            <ScrollView>
                                <SafeAreaView style={{flex: 1}}>
                                    <Card style={styles.card_log}>
                                        <FlatList scrollEnabled={false}
                                            keyExtractor={(item) => item.status_log}
                                            numColumns={1}
                                            data={logPenjualan}
                                            renderItem={renderLogPenjualan} />
                                    </Card>
                                </SafeAreaView>
                            </ScrollView>
                        </View>
                        {logPengiriman == "" ? 
                            <View></View>
                            :
                            <View>
                                <Card style={styles.card_log}>
                                    <View style={{flexDirection: 'row'}}>
                                        <View style={{flex: 1}}>
                                        <Text style={{color: 'black', marginHorizontal: 10, marginVertical: 3, textAlign: 'left'}}>Jasa Pengiriman</Text>
                                        <Text style={{color: 'black', marginHorizontal: 10, marginVertical: 3, textAlign: 'left'}}>Nomor Resi</Text>
                                        </View>
                                        <View style={{flex: 1}}>
                                            <Text style={{color: 'black', marginHorizontal: 10, marginVertical: 3, textAlign: 'right'}}>{barang.map(item => item.jasa_pengiriman)}</Text>
                                            <Text style={{color: 'black', marginHorizontal: 10, marginVertical: 3, textAlign: 'right'}}>{barang.map(item => item.nomor_resi)}</Text>
                                        </View>
                                    </View>
                                </Card>
                                <SafeAreaView>
                                    <Card style={styles.card_log}>
                                        <ScrollView style={{flex: 1}}>    
                                            <FlatList scrollEnabled={false}
                                                keyExtractor={(item) => item.date}
                                                numColumns={1}
                                                data={logPengiriman}
                                                renderItem={renderLogPengiriman} />
                                        </ScrollView>
                                    </Card>
                                </SafeAreaView>
                            </View>
                        }
                    </ScrollView>
                    { 
                        onDelivered == 'DELIVERED' ?
                        // onDelivered == '' ?
                            <View>
                                <Appbar style={styles.bottom}>
                                    <View style={{flex: 1, marginHorizontal: 5, flexDirection: 'row'}}>
                                        <View style={{flex: 1}}>
                                            <Button mode="contained" onPress={() => navigation.navigate('KomplainScreen', {id_penjualan: (barang.map(item => item.id_penjualan)), from: 'DELIVERY ON DELIVERED'})}>Komplain</Button>
                                        </View>
                                        <View style={{flex: 1}}>
                                            <Button mode="contained" onPress={() => setDelivered()}>Pesanan Diterima</Button>
                                        </View>
                                    </View>
                                </Appbar>
                            </View>
                            :
                            barang.map(item => item.status) == 'COMPLAINT' ?
                                <Appbar style={styles.bottom}>
                                    <View style={{flex: 1, marginHorizontal: 5, flexDirection: 'row'}}>
                                        <View style={{flex: 1}}>
                                            <Button mode="contained" onPress={() => navigation.navigate('KomplainScreen', {id_penjualan: (barang.map(item => item.id_penjualan)), from: (barang.map(item => item.status))})}>Lihat Komplain</Button>
                                        </View>
                                    </View>
                                </Appbar>
                                :
                                barang.map(item => item.status) == 'DELIVERY' ?
                                    <Appbar style={styles.bottom}>
                                        <View style={{flex: 1, marginHorizontal: 5, flexDirection: 'row'}}>
                                            <View style={{flex: 1}}>
                                                <Button mode="contained" onPress={() => navigation.navigate('KomplainScreen', {id_penjualan: (barang.map(item => item.id_penjualan)), from: (barang.map(item => item.status))})}>Komplain</Button>
                                            </View>
                                        </View>
                                    </Appbar>
                                    :
                                    barang.map(item => item.status) == 'DELIVERED' ?
                                    <Appbar style={styles.bottom}>
                                        <View style={{flex: 1, marginHorizontal: 5, flexDirection: 'row'}}>
                                            <View style={{flex: 1}}>
                                                <Button mode="contained" onPress={() => navigation.navigate('KomplainScreen', {id_penjualan: (barang.map(item => item.id_penjualan)), from: (barang.map(item => item.status))})}>Komplain</Button>
                                            </View>
                                        </View>
                                    </Appbar>
                                    :
                                    <View>
                                    </View>
                    }       
                    
                </View>
            }
        </View>
    )
}
const styles = StyleSheet.create({
    appbar_total:{
        bottom: 0,
        marginHorizontal: 10,
        borderRadius: 15,
        height: 50,
        backgroundColor: 'white'
    },
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
        marginTop: 10,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 125,
    },
    card_log:{
        marginTop: 10,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 5,
        borderWidth: 1,
    },
    card_total_harga:{
        marginTop: 10,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
    },
    card_alamat:{
        marginTop: 10,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 80,
    },
    card_status:{
        marginTop: 10,
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
        height: 40,
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
    image:{
        width: 120,
        height: 120,
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
    loading: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 10
    },
    bottom_appbar: {
        position: 'absolute',
        left: 0,
        backgroundColor: 'transparent',
        right: 0,
        bottom: 0,
    },
    bottom: {
        position: 'absolute',
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'white'
      },
    bottom_button: {
        marginHorizontal: 10
    }
})
