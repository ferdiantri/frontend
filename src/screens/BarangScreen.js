import React, { useEffect, useState } from 'react';
import { Text, StyleSheet, View, Image, TextInput, Dimensions, FlatList, Alert, ScrollView, SafeAreaView, TouchableOpacity } from 'react-native';
import { Button, Card, Appbar } from 'react-native-paper';
import { Input } from 'react-native-elements';
import axios from 'axios';
import { Picker } from '@react-native-picker/picker';
import { SwiperFlatList } from 'react-native-swiper-flatlist';
import Material from 'react-native-vector-icons/MaterialCommunityIcons';
import {Shapes} from 'react-native-background-shapes';
import Carousel from 'react-native-snap-carousel';



export default function BarangScreen({ navigation }) {
    const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    const [data, setData] = useState([]);
    const [banner, setBanner] = useState([]);
    const [column, setColumn] = useState(2);
    const [selectedSort, setSelectedSort] = useState('relevan');
    // const isCarousel = React.useRef(null)
    // const sliderWidth = Dimensions.get('window').width
    // const itemWidth = Math.round(sliderWidth)
    

    async function agenda(){
        setColumn('1');
    }
    async function grid(){
        setColumn('2');
    }
    
    const renderItem = ({item}) => (   
    <View style={{flex: 1}}>
        <TouchableOpacity onPress={() => {
            navigation.navigate('DetailBarangScreen', {
                id_barang: (item.id_barang),
            })
        }}>
            {
                item.stok_barang <= 0 ?
                <View>
                    <Card style={styles.card_stok_empty}>
                        {
                            item.harga <= 0 ?
                            <Text style={{transform: [{ rotate: '-30deg'}], width: 75, color: 'white', fontWeight: 'bold'}}>Segera!</Text>
                            :
                            <Text style={{transform: [{ rotate: '-30deg'}], width: 75, color: 'white', fontWeight: 'bold'}}>Terjual!</Text>
                        }
                        <Image source={{ uri: 'http://192.168.43.22:8000/'+item.gambar,}} style={styles.image}/>
                        <Text style={styles.judul_barang}>{item.nama_barang} {item.ram}GB/{item.internal}GB - {item.warna}</Text>
                        <Text style={styles.judul_barang}>{numberFormat(item.harga)}</Text>
                        <Text style={{marginVertical: 1, marginHorizontal: 10, color: "black", textAlign: 'right'}}>Terjual {item.terjual}</Text>
                    </Card>
                </View>
                :
                <View>
                    <Card style={styles.card}>
                        <Image source={{ uri: 'http://192.168.43.22:8000/'+item.gambar,}} style={styles.image}/>
                        <Text style={styles.judul_barang}>{item.nama_barang} {item.ram}GB/{item.internal}GB - {item.warna}</Text>
                        <Text style={styles.judul_barang}>{numberFormat(item.harga)}</Text>
                        <Text style={{marginVertical: 1, marginHorizontal: 10, color: "black", textAlign: 'right'}}>Terjual {item.terjual}</Text>
                    </Card>
                </View>
            }
            
        </TouchableOpacity>
    </View>
    )
    async function getBarang(itemValue){
        let headers = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }
        axios.get('http://192.168.43.22:8000/api/user/barang', headers).then(response => {
        console.log(response.data);
            if(itemValue == 'relevan' || selectedSort == 'relevan'){
                setData(response.data.sort((a, b) => {a.nama_barang.localeCompare(b.nama_barang), b.stok_barang - a.stok_barang}));
            }
            if(itemValue == 'terendah'){
                setData(response.data.sort((a, b) => a.harga - b.harga));
            }
            if(itemValue == 'tertinggi'){
                setData(response.data.sort((a, b) => b.harga - a.harga));
            }
            if(itemValue == 'terlaris'){
                setData(response.data.sort((a, b) => b.terjual - a.terjual));
            }
        });
        axios.get('http://192.168.43.22:8000/api/user/banner', headers).then(response => {
            console.log(response.data);
            setBanner(response.data);
        });
    }
    useEffect(() => {
        // const unsubscribe = navigation.addListener('focus', () => {
        //     getBarang()
        // });

        getBarang()
    },[]);
            return(

                <View style={{flex: 1, marginBottom: 55}}>
                    <ScrollView style={{flex: 1}}>
                        <Shapes
                            primaryColor="#00aced"
                            secondaryColor="#2F53D5"
                            height={3}
                            borderRadius={50}
                            style={{height: 75}}
                            figures={[
                            {name: 'circle', position: 'flex-start', axis: 'top', size: 105},
                            {name: 'circle', position: 'center', axis: 'top', size: 30},
                            ]}
                        />
                        <View>
                            <View style={{ flexDirection: 'row' }}>
                                {/* <View style={{ flex: 2, alignItems: 'center', justifyContent: 'center', marginTop: 4 }}>
                                    <Logo name='checkmark-done' size={50} color='#00aced'/>
                                </View> */}
                                <View style={{ flex: 9 }}>
                                    <TextInput
                                        placeholder='Cari Barang Disini'
                                        placeholderTextColor="grey"
                                        pointerEvents="none"
                                        underlineColorAndroid='transparent'
                                        style={styles.form}
                                        onPressIn={() => {navigation.navigate('SearchBarangScreen')}}
                                        theme={{ roundness: 15 }} />
                                </View>
                            </View>
                            <View style={styles.container}>
                                <SwiperFlatList
                                autoplayLoop
                                data={banner}
                                renderItem={({ item }) => (
                                    <TouchableOpacity onPress={() => navigation.navigate('BannerScreen', {id_banner: (item.id_banner)})}>
                                        <View style={[styles.child, { backgroundColor: item }]}>
                                            <Image source={{ uri: 'http://192.168.43.22:8000/'+item.gambar,}} style={styles.image_banner}/>
                                        </View>
                                    </TouchableOpacity>
                                )}
                                />
                            </View>
                            {/* <Carousel
                                ref={isCarousel}
                                autoplay
                                loop
                                data={banner}
                                renderItem={({ item }) => (
                                    <TouchableOpacity onPress={() => {
                                        Alert.alert(item.id_banner);
                                    }}>
                                        <View style={[styles.child, { backgroundColor: item, }]}>
                                            <Image source={{ uri: 'http://192.168.43.22:8000/'+item.gambar,}} style={styles.image_banner}/>
                                        </View>
                                    </TouchableOpacity>
                                )}
                                style={{marginHorizontal: 15}}
                                sliderWidth={sliderWidth}
                                itemWidth={itemWidth}
                                /> */}
                            <View style={{flexDirection: 'row', flex : 1, alignItems: 'flex-end', justifyContent: 'flex-end', marginHorizontal: 5, marginTop: 12}}>
                                <View>
                                    <Picker
                                        style={{ height: 40, width: 200, color: 'black' }}
                                        selectedValue={selectedSort}
                                        onValueChange={(itemValue, itemIndex) =>{
                                            setSelectedSort(itemValue);
                                            getBarang(itemValue);
                                            }
                                        }>
                                        <Picker.Item label="Relevan" value="relevan" />
                                        <Picker.Item label="Harga Terendah" value="terendah" />
                                        <Picker.Item label="Harga Tertinggi" value="tertinggi" />
                                        <Picker.Item label="Terlaris" value="terlaris" />
                                    </Picker>
                                </View>
                                <View>
                                    {column == 1 ?
                                        <View style={{flex : 1}}>
                                            <TouchableOpacity onPress={grid}>
                                                <Material name='view-grid' size={30} color='black'/>
                                            </TouchableOpacity>
                                        </View>
                                        :
                                        <View style={{flex : 1}}>
                                            <TouchableOpacity onPress={agenda}>
                                                <Material name='view-agenda' size={30} color='black'/>
                                            </TouchableOpacity>
                                        </View>
                                    }
                                </View>
                            </View>
                            <View>
                                {/* <Card style={{backgroundColor: 'white', borderWidth: 1, marginHorizontal: 2, marginVertical: 2}}> */}
                                    <FlatList scrollEnabled={false}
                                        keyExtractor={(item) => item.id_barang}
                                        numColumns={column}
                                        key={column}
                                        data={data}
                                        renderItem={renderItem} />
                                {/* </Card> */}
                            </View>
                        </View>
                    </ScrollView>
                </View>
            )
}

const { width } = Dimensions.get('window');
const styles = StyleSheet.create({
    card:{
        marginHorizontal: 2,
        marginVertical: 2,
        backgroundColor: 'white',
        borderRadius: 5,
        borderWidth: 1,
        height: 270,
    },
    card_stok_empty:{
        marginHorizontal: 2,
        marginVertical: 2,
        backgroundColor: 'grey',
        elevation: 1,
        borderRadius: 5,
        borderWidth: 1,
        height: 270,
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
        borderRadius: 15,
        height: 40,
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
        marginTop: 35,
        height: 150,
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
        marginVertical: 1,
        marginHorizontal: 10,
        color: "black"
    },
})

  