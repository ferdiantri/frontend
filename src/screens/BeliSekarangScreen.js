import React, { useEffect, useState } from 'react'
import { Text, ImageBackground, Image, ActivityIndicator, FlatList, View, StyleSheet, ScrollView, TouchableOpacity, TextInput, Alert, ToastAndroid } from 'react-native';
import { Card, Appbar, Button } from 'react-native-paper'
import axios from 'axios'
import EncryptedStorage from 'react-native-encrypted-storage'
import { Picker } from '@react-native-picker/picker'
import Icon from 'react-native-vector-icons/Feather';
import Material from 'react-native-vector-icons/MaterialCommunityIcons';


export default function PembelianScreen({ navigation, route }){
    const { id_barang } = route.params;
    const [jumlah, setJumlah] = useState(1);
    const [data, setData] = useState([]);
    const [selectedKurir, setselectedKurir] = useState();
    const [alamat, setAlamat] = useState([]);
    const [serviceKurir, setServiceKurir] = useState([]);
    const [cost, setCost] = useState([]);
    const [voucher, setVoucher] = useState([]);
    const [dataVoucher, setDataVoucher] = useState([]);
    const [errorVoucher, setErrorVoucher] = useState();
    const [errorSelectedKurir, setErrorSelectedKurir] = useState();
    const [loading, setLoading] = useState(true);
    const [emptyAlamat, setEmptyAlamat] = useState(true);

    async function getBill(){
        if(alamat == false){
            alert('Tambah Alamat Terlebih Dahulu');
        }
        if(!selectedKurir){
            console.log("Pilih Jasa Pengiriman");
            setErrorSelectedKurir("Pilih Jasa Pengiriman")
        }
        else{
            const email = await EncryptedStorage.getItem("email");
            setErrorSelectedKurir("")
            const dataPembelian = {
                email : email,
                id_barang : data.map(barang => barang.id_barang)[0],
                id_alamat : alamat.map(alamat => alamat.id_alamat)[0],
                jasa_pengiriman : selectedKurir,
                ongkir : serviceKurir,
                harga_barang : data.map(item => item.harga*jumlah)[0],
                jumlah_barang : jumlah,
                potongan : potongan[0],
                id_voucher : id_voucher[0],
                total_harga : total_harga
            }
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            console.log(dataPembelian)
            try{
                axios.post(`http://192.168.43.22:8000/api/user/tambah_penjualan_beli_sekarang`, dataPembelian, headers ).then(response => 
                    {
                        console.log(response.data)
                        if(response.data.error){
                            alert(response.data.error)
                        }
                        else{
                            navigation.navigate('PembayaranScreen', {
                                link_invoice: (response.data.invoice.invoice_url),
                            })
                        }
                    }    
                )
            }
            catch(err){
                console.log(err);
            }
        }
    }
    async function tambah(){
        if(stok_barang <= jumlah){
            Alert.alert('Stok Barang Tidak Mencukupi');
        }
        else{
            setJumlah(jumlah+1);
        }
    }
    async function kurang(){
        if(jumlah <= 1){
            Alert.alert('Minimum Pembelian 1');
        }
        else{
            setJumlah(jumlah-1);
        }
    }
    async function getVoucher(){
        console.log(voucher);
        if(!voucher){
            console.log("Tidak Boleh Kosong");
        }
        else{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                }
            }
            axios.post(`http://192.168.43.22:8000/api/user/voucher`, {kode : voucher}, headers).then(response => {
                console.log(response.data);
                const dataVoucher = response.data;
                if(response.data.error){
                    const errorVoucher = response.data.error
                    setErrorVoucher(errorVoucher);
                }
                else{
                    const errorVoucher = 'Berhasil';
                    setErrorVoucher(errorVoucher);
                    setDataVoucher(dataVoucher);
                }
            })
        }
    }
    const renderItem = ({item}) => (   
        <View style={{flex: 1}}>
                <Card style={styles.card_barang}>
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
                                    <Text style={styles.judul_barang}>{numberFormat(item.harga)}</Text>
                                </View>
                                <View style={{ flexDirection: 'row', marginVertical: 5}}>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <TouchableOpacity onPress={() => kurang()}>
                                            <Icon name='minus' size={30} color='black' />
                                        </TouchableOpacity>
                                    </View>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <Text style={{fontSize: 20, color: 'black'}}>{jumlah}</Text>
                                    </View>
                                    <View style={{flex: 1, alignItems:'center', justifyContent: 'center'}}>
                                        <TouchableOpacity onPress={() => tambah()}>
                                            <Icon name='plus' size={30} color='black' />
                                        </TouchableOpacity>
                                    </View>
                                </View>
                            </View>
                        </View>     
                    </Card>
        </View>
    )
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    async function getKeranjang(){
        const token = await EncryptedStorage.getItem("token");
        if(token == null){
            navigation.replace('LoginScreen');
        }
        else{

        }
        try{
            let headers = {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                  'Authorization': 'Bearer '+token
                }
            }
            axios.get('http://192.168.43.22:8000/api/user/profile', headers).then(response => {
                const email = response.data.profile.email;
                console.log(email);
                   axios.get('http://192.168.43.22:8000/api/user/detail_barang',{params: {id_barang: id_barang}}, headers).then(response => {
                    console.log(response.data);
                    setData(response.data);
                })
                 axios.get(`http://192.168.43.22:8000/api/user/alamat?email=${email}`).then(async(response) => {
                    console.log(response.data);
                    if(response.data.error){
                        setEmptyAlamat(true);
                        console.log('All Err')
                    }
                    if(response.data.success){
                        setAlamat(response.data.success);
                        console.log('All Ben')
                        setEmptyAlamat(false);
                    }
                })
                .finally(() => setLoading(false));
            })
        }
        catch(err){
               console.log(err);
        }   
    }
    useEffect(() => {
        const unsubscribe = navigation.addListener('focus', () => {
            getKeranjang()
        });
        getKeranjang()
        unsubscribe;
    },[emptyAlamat]);
        async function pilihKurir(itemValue){
            const data = {
                origin : 164,
                destination : alamat.map(item => item.city_id)[0],
                weight : 1000,
                courier : itemValue
            };
            console.log(data);
            let headers = {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'key': 'f151ce36d48ee772f269a04808984ca8',
                }
            }
            console.log(data);
            axios.post(`https://api.rajaongkir.com/starter/cost`, data, headers).then(response => {
                console.log(response.data.rajaongkir.results);
                setCost(response.data.rajaongkir.results);
            })
        }
        const total_harga_barang = data.map(item => item.harga*jumlah);
        const total_harga_ongkir = total_harga_barang.concat(serviceKurir).reduce((x, y) => (x + y), 0);
        const min_pembelian = dataVoucher.map(item => item.min_pembelian);
        const stok_barang = data.map(item => item.stok_barang);
        const potongan = (total_harga_ongkir >= min_pembelian && errorVoucher == 'Berhasil') ? dataVoucher.map(item => item.potongan): "0";
        const total_harga = (total_harga_ongkir >= min_pembelian) ? total_harga_ongkir-dataVoucher.map(item => item.potongan) : total_harga_ongkir;
        const id_voucher = (total_harga_ongkir >= min_pembelian && errorVoucher == 'Berhasil') ? dataVoucher.map(item => item.id_voucher): "0";
        const min_pembelian_kurang = (total_harga_ongkir <= min_pembelian && errorVoucher == 'Berhasil') ? "Minimal Pembelian Tidak Terpenuhi" : "0";
        return(
            <View style={{flex: 1}}>
                <ImageBackground source={require('../assets/background.png')} style={{flex: 1, width: '100%',}}>
                {
                    loading == true ?
                    <View style={styles.loading}>
                        <ActivityIndicator />
                    </View>
                    :
                    <View>
                        <ScrollView>
                            <View style={{marginTop: 10}}>
                                <Text style={{marginLeft: 15, fontSize: 18, color: 'black', fontWeight: 'bold'}}>Alamat Pengiriman</Text>
                                {
                                    emptyAlamat == true ? 
                                    <TouchableOpacity onPress={() => navigation.navigate('AlamatScreen')}>
                                        <Card style={styles.card_alamat}>
                                            <Text style={{ marginLeft: 10, color: 'black', marginHorizontal: 10, marginVertical: 30, textAlign: 'center', textAlign: 'center'}}>Tambah Alamat</Text>
                                        </Card>
                                    </TouchableOpacity>
                                    :
                                    <Card style={styles.card_alamat}>
                                        <TouchableOpacity onPress={() => navigation.navigate('AlamatScreen')}>
                                            <View style={{flexDirection: 'row'}}>
                                                <View style={{flex: 7}}>
                                                    <Text style={{ marginLeft: 10, color: 'black', marginTop: 10}}>{alamat.map(item => item.nama_penerima)} ({alamat.map(item => item.nomor_telepon)})</Text>
                                                    <Text style={{ marginLeft: 10, color: 'black',}}>{alamat.map(item => item.alamat)}, {alamat.map(item => item.kecamatan)}</Text>
                                                    <Text style={{ marginLeft: 10, color: 'black',}}>{alamat.map(item => item.kabupaten)}, {alamat.map(item => item.provinsi)}, {alamat.map(item => item.kode_pos)}</Text>
                                                </View>
                                                <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
                                                    <Material name='pencil-circle' size={30} color='black'/>
                                                </View>
                                            </View>
                                            
                                        </TouchableOpacity>
                                    </Card>
                                }
                            </View>
                            <View style={{marginTop: 10}}>
                                <Text style={{marginLeft: 15, fontSize: 18, color: 'black', fontWeight: 'bold'}}>Detail Barang</Text>
                                    <FlatList scrollEnabled={false}
                                        keyExtractor={(item) => item.id_barang}
                                        numColumns={1}
                                        data={data}
                                        renderItem={renderItem} />
                            </View>
                            <View>
                                
                                {
                                    emptyAlamat == true ?
                                    <View>
                
                                    </View>
                                    :
                                    <View>
                                        <Text style={{marginLeft: 15, fontSize: 18, marginTop: 10, color: 'black', fontWeight: 'bold'}}>Jasa Pengiriman</Text>
                                        <Card style={styles.card_kurir}>
                                            <Picker
                                                selectedValue={selectedKurir}
                                                style={{ height: 40, width: 250, color: 'black' }}            
                                                onValueChange={(itemValue, itemIndex) => {
                                                if (!itemValue) {
                                                    return;
                                                }
                                                setselectedKurir(itemValue)
                                                pilihKurir(itemValue)
                                            }}>
                                            <Picker.Item label="Pilih Jasa Pengiriman" value="" />              
                                            <Picker.Item label="JNE" value="jne" /> 
                                            <Picker.Item label="POS INDONESIA" value="pos" />   
                                            <Picker.Item label="TIKI" value="tiki" />                        
                                            </Picker>
                                        </Card>
                                        {
                                            selectedKurir == null ? 
                                            <View></View>
                                            :
                                            <Card style={styles.card_kurir}>
                                            <Picker
                                                selectedValue={serviceKurir}
                                                style={{ height: 40, width: 250, color: 'black' }}              
                                                onValueChange={(itemValue, itemIndex) => {
                                                if (!itemValue) {
                                                    return;
                                                }
                                                setServiceKurir(itemValue)
                                                console.log(itemValue);

                                                // console.log(cost.map(item => item.costs.map(element => element.cost.map(harga => harga.value)[itemIndex]).service));
                                                // const filtered = cost.filter(item => item.costs.filter(ite => ite.cost.filter(c => c.value = [itemIndex])));
                                                // console.log(filtered.map(item => item.costs.map(srv => srv.service)));
                                                }}>
                                                {/* {cost.map(item => {
                                                    return item.costs.map(element => {
                                                        return (<Picker.Item key={element.code} label={element.service} value={element.cost.map(cost => cost.value)}/>)
                                                    })
                                                })} */}
                                                {cost.map(item => {
                                                    return item.costs.map(element => {
                                                        return element.cost.map(harga => {
                                                            return (<Picker.Item key={element.code} label={element.service} value={harga.value}/>)
                                                        })
                                                    })
                                                })}
                                            </Picker>                    
                                        </Card>
                                        }
                                        <View style={{flex: 1}}>
                                            <Text style={{fontSize: 15, textAlign:'right', marginHorizontal: 10}}>{errorSelectedKurir}</Text>
                                        </View>
                                    </View>
                                }
                            </View>
                            <View style={{flex: 1}}>
                                <Text style={{marginLeft: 15, marginTop: 10,fontSize: 18, color: 'black', fontWeight: 'bold'}}>Voucher</Text>
                                <Card style={styles.card_voucher}>
                                    <View style={{flexDirection: 'row'}}>
                                        <View style={{flex: 2.5, justifyContent: 'center'}}>
                                            <TextInput value={voucher} onChangeText={setVoucher} placeholderTextColor="#000" style={{color: 'black'}} placeholder='Masukkan Kode Voucher'></TextInput>
                                        </View>
                                        <View style={{flex: 1, justifyContent: 'center'}}>
                                            <Button onPress={() => getVoucher()}>Gunakan</Button>
                                        </View>
                                    </View>  
                                </Card>
                            </View>
                            {
                                min_pembelian_kurang == 0 ?
                                <View style={{flex: 1}}>
                                    <Text style={{fontSize: 15, textAlign:'right', marginHorizontal: 10, color: 'black'}}>{errorVoucher}</Text>
                                </View>
                                :
                                <View style={{flex: 1}}>
                                    <Text style={{fontSize: 15, textAlign:'right', marginHorizontal: 10, color: 'black'}}>{min_pembelian_kurang}</Text>
                                </View>
                            }
                            
                            <View style={{flex: 1}}>
                                <Text style={{marginLeft: 15, marginTop: 10,fontSize: 18, color: 'black', fontWeight: 'bold'}}>Detail Harga</Text>
                                <Card style={styles.card_total_harga}>
                                    <View style={{marginHorizontal: 10, marginTop: 5}}>
                                        <View style={{flexDirection: 'row'}}>
                                            <Text style={{flex: 1, color: "black",}}>Total Harga Barang</Text>
                                            <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(total_harga_barang)}</Text>
                                        </View>
                                        <View style={{flexDirection: 'row'}}>
                                            <Text style={{flex: 1, color: "black",}}>Ongkos Kirim</Text>
                                            <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(serviceKurir)}</Text>
                                        </View>
                                        <View style={{flexDirection: 'row'}}>
                                            <Text style={{flex: 1, color: "black",}}>Voucher dan Potongan</Text>
                                            <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(potongan)}</Text>
                                        </View>
                                        <View style={{flexDirection: 'row'}}>
                                            <Text style={{flex: 1, color: "black",}}>Total Tagihan</Text>
                                            <Text style={{flex: 1, color: "black", textAlign: 'right'}}>{numberFormat(total_harga)}</Text>
                                        </View>
                                    </View>
                                </Card>
                            </View>
                            <View>
                                <View style={{marginHorizontal: 10, marginTop: 10}}>
                                    <Button mode="contained" onPress={() => getBill()}>Pilih Pembayaran</Button>
                                </View>
                            </View>
                        </ScrollView>
                    </View>
                }
                </ImageBackground>   
            </View>
        )        
}
const styles = StyleSheet.create({
    button:{
        width: 50,
        height: 50,
        borderRadius: 50,
        backgroundColor: 'grey',
        marginTop: 10,
        marginLeft: 5,
        marginRight: 10,
    },
    card_barang:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 8, 
        borderWidth: 1,
        height: 125,
    },
    card_total_harga:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 8, 
        borderWidth: 1,
        height: 90,
    },
    card_alamat:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 8, 
        borderWidth: 1,
        height: 80,
    },
    loading: {
        flex: 1,
        justifyContent: "center",
        flexDirection: "row",
        justifyContent: "space-around",
        padding: 10
    },
    card_kurir:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 8, 
        borderWidth: 1,
        height: 55,
    },
    card_voucher:{
        marginHorizontal: 10,
        backgroundColor: 'white',
        borderRadius: 8, 
        borderWidth: 1,
        height: 55,
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
})